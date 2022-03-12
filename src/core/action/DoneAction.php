<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class DoneAction extends TaskActionTemplate
{
    /**
     * {@inheritdoc}
     */
    public function getActionCode(): string
    {
        return 'complete';
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle(): string
    {
        return 'Завершить';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonColorClass(): string
    {
        return 'request';
    }

    /**
     * Метод проверяет, имеет ли пользователь право завершить задание
     * @param Task $task
     * @return bool
     */
    public static function getUserRightsCheck(Task $task): bool
    {
        var_dump(\Yii::$app->user->id === $task->client_id && $task->status == Task::STATUS_IN_PROGRESS);
        return (\Yii::$app->user->id === $task->client_id && $task->status == Task::STATUS_IN_PROGRESS);
    }
}
