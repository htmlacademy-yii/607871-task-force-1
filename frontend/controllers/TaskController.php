<?php


namespace frontend\controllers;


use yii\web\Controller;

class TaskController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->db->open();
        //return $this->render('index');
        return 'fdhgflhg';
    }
}