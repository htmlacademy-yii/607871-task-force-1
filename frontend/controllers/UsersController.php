<?php


namespace frontend\controllers;


use frontend\models\User;
use yii\web\Controller;

class UsersController extends Controller
{
    public function actionIndex()
    {
        $users = User::getAllExecutors();

        return $this->render('index', ['users' => $users]);
    }
}