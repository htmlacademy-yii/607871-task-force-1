<?php

namespace frontend\controllers;

use frontend\models\Auth;
use frontend\models\forms\TaskSearchForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\Task;
use frontend\models\VerifyEmailForm;
use frontend\service\VKontakteAuthService;
use TaskForce\components\AuthHandler;
use Yii;
use yii\web\BadRequestHttpException;
use \frontend\models\forms\LoginForm;


/**
 * Site controller
 */
class SiteController extends SecuredController

{
    public $layout = 'landing';


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    /**
     * Метод отвечает за отобажение главной страницы сайта.
     * @return string
     */
    public function actionIndex()
    {
        $tasks = Task::find()->joinWith('category')->orderBy(['creation_date' => SORT_DESC])->limit(4)->all();
        $model = new TaskSearchForm();
        return $this->render('index', ['tasks' => $tasks, 'model' => $model]);
    }

    /**
     * Метод отвечает за аутентификацию клиента
     * @return array|\yii\web\Response
     * @throws BadRequestHttpException
     */
    public function actionLogin()
    {
        $loginForm = new LoginForm();
        if (\Yii::$app->request->getIsPost()) {
            $loginForm->load(\Yii::$app->request->post());
            $user = $loginForm->getUser();

            if ($loginForm->validate()) {
                \Yii::$app->user->login($user);
                \Yii::$app->session->set('city_id', \Yii::$app->user->identity->profile->city_id);
                return $this->redirect('/tasks');
            }
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($loginForm->load(\Yii::$app->request->post())) {
                return \yii\widgets\ActiveForm::validate($loginForm);
            }
        }
        throw new BadRequestHttpException('Неверный запрос!');
    }

    /**
     * Метод авторизации пользователя через соц. сеть Вконтакте, если соц. сеть прислала положительный ответ.
     * @param $client
     * @return \yii\web\Response
     */
    public function onAuthSuccess($client)
    {
        if (!\Yii::$app->user->isGuest) {
            $this->redirect('/tasks');
        }

        $attributes = $client->getUserAttributes();

        /* @var $auth Auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        // авторизация
        if ($auth) {
            $user = $auth->user;
            \Yii::$app->user->login($user);
            \Yii::$app->session->set('city_id', \Yii::$app->user->identity->profile->city_id);
        } else {
            //регистрация
            $vKontakteService = new VKontakteAuthService($client);
            $vKontakteService->register();
        }
        return $this->redirect('/tasks');
    }
}