<?php


namespace frontend\controllers;


use frontend\models\Auth;
use frontend\models\City;
use frontend\models\forms\LoginForm;
use frontend\models\forms\TaskSearchForm;
use frontend\models\Profile;
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
                    $password = \Yii::$app->security->generateRandomString(10);
                    $user = new User([
                        'scenario' => User::SCENARIO_CREATE_USER,
                        'email' => $attributes['email'],
                        'password' => $password,
                        'name' => implode(' ', array($attributes['last_name'], $attributes['first_name'])),

                    ]);

                    $city = City::find()->where(['name' => $attributes['city']['title']])->one();
                    $profile = new Profile([
                        'scenario' => Profile::SCENARIO_DEFAULT,
                        'city_id' => $city->id,
                        'avatar' => $attributes['photo'],
                        'birth_date' => date('Y-m-d', strtotime($attributes['bdate'])),
                    ]);

                    $user->validate();
                    $profile->validate();

                    if (!$user->errors && !$profile->errors) {
                        $transaction = \Yii::$app->db->beginTransaction();
                        try {
                            $user->save();
                            $profile->user_id = $user->getPrimaryKey();
                            $profile->save();
                            $auth = new Auth([
                                'user_id' => $user->getPrimaryKey(),
                                'source' => $client->getId(),
                                'source_id' => (string)$attributes['id'],
                            ]);
                            $auth->save();
                            $transaction->commit();
                            \Yii::$app->user->login($user);
                        } catch (\Throwable $e) {
                            $transaction->rollBack();
                        }
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