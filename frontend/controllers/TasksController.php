<?php


namespace frontend\controllers;

use frontend\models\forms\TaskSearchForm;
use frontend\models\Task;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $searchForm = new TaskSearchForm();
        $searchForm->load(\Yii::$app->request->get());
        return $this->render('index', ['dataProvider' => $searchForm->getDataProvider(), 'model' => $searchForm]);
    }

    public function actionView($id)
    {
        $task = Task::find()
            ->with('client')
            ->where('task.id =:id', ['id' => $id])
            ->one();

        if(!$task) {
            throw new NotFoundHttpException("Задача не найдена!");
        }

        $searchForm = new TaskSearchForm();
        return $this->render('view', [
            'task' => $task,
            'model' => $searchForm,
            ]);
    }
}