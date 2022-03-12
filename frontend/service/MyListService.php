<?php


namespace frontend\service;


use frontend\models\Task;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class MyListService
{
    /**
     * Метод возвращает ActiveDataProvider для страницы "Мои задания".
     * @param int $currentUserId
     * @param $status
     * @return ActiveDataProvider
     */
    public static function dataProvider(int $currentUserId, $status)
    {
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
            $query->andWhere(['in', 'status', [Task::STATUS_CANCELED, Task::STATUS_FAILED]]);
        }

        if ($status === 'hidden') {
            $query->andWhere(['status' => Task::STATUS_IN_PROGRESS])->andWhere(['<=', 'due_date', (new Expression("NOW()"))]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5
            ],
            'sort' => [
                'defaultOrder' => [
                    'creation_date' => SORT_DESC,
                ]
            ],
        ]);
    }
}