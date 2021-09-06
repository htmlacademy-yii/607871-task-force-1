<?php


namespace frontend\controllers;


use frontend\models\Category;
use frontend\models\Profile;
use frontend\models\Task;
use frontend\models\User;
use yii\web\Controller;

class TaskController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->db->open();
        $user = User::findOne(10);
        $portfolios = $user->profile;

        //var_dump($portfolios);
        return $this->render('index', ['portfolios' => $portfolios]);

    }
}