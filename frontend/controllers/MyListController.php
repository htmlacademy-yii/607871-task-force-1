<?php


namespace frontend\controllers;


use frontend\models\Task;
use yii\db\Expression;

class MyListController extends SecuredController
{
    public function actionIndex()
    {
        $currentUserId = \Yii::$app->user->id;
        $status = \Yii::$app->request->get('status');

        $query = Task::find()->where(['or', "client_id={$currentUserId}", "executor_id={$currentUserId}"]);

        if ($status === null || $status === 'new') {
            $query->andWhere(['status' => Task::STATUS_NEW])->andWhere(['client_id' => $currentUserId]);
        }

        if ($status === 'completed') {
            $query->andWhere(['status' => Task::STATUS_FINISHED]);
        }

        if ($status === 'active') {
            $query->andWhere(['status' => Task::STATUS_IN_PROGRESS]);
        }

        if ($status === 'canceled') {
            $query->andWhere(['in', 'status',[Task::STATUS_CANCELED, Task::STATUS_FAILED]]);
        }

        if ($status === 'hidden') {
            $query->andWhere(['status' => Task::STATUS_IN_PROGRESS])->andWhere(['<=', 'due_date', (new Expression("NOW()"))]);
        }

        $myTasks = $query->all();
        return $this->render('index', ['status' => $status, 'myTasks' => $myTasks]);
    }

}