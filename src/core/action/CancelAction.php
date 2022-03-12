<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class CancelAction extends TaskActionTemplate
{
    /**
     * {@inheritdoc}
     */
    public function getActionCode(): string
    {
        return 'cancel';
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle(): string
    {
        return 'Отменить';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonColorClass(): string
    {
        return 'cancel';
    }

    /**
     * Метод проверяет, имеет ли пользователь право отказаться от выполнения задания.
     * @param Task $task
     * @return bool
     */
    public static function getUserRightsCheck(Task $task): bool
    {
        return \Yii::$app->user->id === $task->client_id && $task->status == Task::STATUS_NEW;
    }
}
