<?php


namespace frontend\controllers;


use frontend\models\forms\TaskSearchForm;
use frontend\models\Task;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex()
    {
        $searchForm = new TaskSearchForm();
        if (\Yii::$app->request->getIsGet()) {
            if($searchForm->load(\Yii::$app->request->get())) {
                $newTasks = $searchForm->getDataProvider();

            } else {
                $newTasks = Task::find()
                    ->where(['executor_id' => NULL])
                    ->joinWith('category')
                    ->orderBy(['creation_date' => SORT_DESC])->all();
            }

            return $this->render('index', ['newTasks' => $newTasks, 'model' => $searchForm]);

        }
    }
}