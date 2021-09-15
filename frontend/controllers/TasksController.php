<?php


namespace frontend\controllers;


use frontend\models\Task;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex()
    {
        \Yii::$app->db->open();
        $newTasks = Task::find()
            ->where(['executor_id' => NULL])
            ->joinWith('category')
            ->orderBy(['creation_date' => SORT_DESC])->all();

        return $this->render('index', ['newTasks' => $newTasks]);

    }
}