<?php


namespace frontend\controllers;


use frontend\models\forms\TaskSearchForm;
use frontend\models\forms\UserSearchForm;
use frontend\models\Task;
use frontend\models\User;
use yii\web\Controller;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $searchForm = new UserSearchForm();
        if (\Yii::$app->request->getIsGet()) {
            if ($searchForm->load(\Yii::$app->request->get())) {
                $users = $searchForm->getDataProvider();

            } else {
                $users = User::getAllExecutors();
            }
        }
       // $users = $searchForm->getDataProvider();
        var_dump($users);

        //return $this->render('index', ['users' => $users, 'model' => $searchForm]);
    }
}