<?php


namespace frontend\controllers;


use frontend\models\forms\LoginForm;
use frontend\models\forms\TaskSearchForm;
use frontend\models\Task;

class MainController extends SecuredController
{
    public $layout = 'landing';

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

}