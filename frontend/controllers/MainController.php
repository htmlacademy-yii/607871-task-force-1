<?php


namespace frontend\controllers;


use frontend\models\Auth;
use frontend\models\forms\LoginForm;
use frontend\models\forms\TaskSearchForm;
use frontend\models\Task;
use frontend\models\User;
use GuzzleHttp\Client;
use yii\authclient\clients\VKontakte;

class MainController extends SecuredController
{
    public $layout = 'landing';

    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function actionIndex()
    {
        $tasks = Task::find()->joinWith('category')->orderBy(['creation_date' => SORT_DESC])->limit(4)->all();
        $model = new TaskSearchForm();
        return $this->render('index', ['tasks' => $tasks, 'model' => $model]);
    }

    public function actionLogin()
    {
        $loginForm = new LoginForm();

        if (\Yii::$app->request->getIsPost()) {
            $loginForm->load(\Yii::$app->request->post());
            if ($loginForm->validate()) {
                $user = $loginForm->getUser();
                \Yii::$app->user->login($user);
                return $this->redirect('/tasks');
            }
        }

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            if ($loginForm->load(\Yii::$app->request->post())) {
                return \yii\widgets\ActiveForm::validate($loginForm);
            }
        }
        throw new \yii\web\BadRequestHttpException('Неверный запрос!');
    }

    public function actionLoginVkontakte()
    {

 //$this->redirect('https://oauth.vk.com/authorize?client_id=8084301&redirect_uri=http://yii-taskforce/loginvk&display=popup&response_type=code');

    }
    public function onAuthSuccess($client)
    {
        $attributes = $client->getUserAttributes();

        /* @var $auth Auth */
        $auth = Auth::find()->where([
            'source' => $client->getId(),
            'source_id' => $attributes['id'],
        ])->one();

        if (\Yii::$app->user->isGuest) {
            if ($auth) { // авторизация
                $user = $auth->user;
                \Yii::$app->user->login($user);
            } else { // регистрация
                if (isset($attributes['email']) && User::find()->where(['email' => $attributes['email']])->exists()) {
                    \Yii::$app->getSession()->setFlash('error', [
                        \Yii::t('app', "Пользователь с такой электронной почтой как в {client} уже существует, но с ним не связан. Для начала войдите на сайт использую электронную почту, для того, что бы связать её.", ['client' => $client->getTitle()]),
                    ]);
                } else {
                    $password = \Yii::$app->security->generateRandomString(6);
                    $user = new User([
                        'email' => $attributes['email'],
                        'password' => $password,
                    ]);
                    $transaction = $user->getDb()->beginTransaction();
                    if ($user->save()) {
                        $auth = new Auth([
                            'user_id' => $user->id,
                            'source' => $client->getId(),
                            'source_id' => (string)$attributes['id'],
                        ]);
                        if ($auth->save()) {
                            $transaction->commit();
                            \Yii::$app->user->login($user);
                        } else {
                            print_r($auth->getErrors());
                        }
                    } else {
                        print_r($user->getErrors());
                    }
                }
            }
        } else { // Пользователь уже зарегистрирован
            if (!$auth) { // добавляем внешний сервис аутентификации
                $auth = new Auth([
                    'user_id' => \Yii::$app->user->id,
                    'source' => $client->getId(),
                    'source_id' => $attributes['id'],
                ]);
                $auth->save();
            }
        }
    }

}