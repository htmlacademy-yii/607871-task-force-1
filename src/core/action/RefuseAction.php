<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class RefuseAction extends TaskActionTemplate
{
    /**
     * {@inheritdoc}
     */
    public function getActionCode(): string
    {
        return 'refuse';
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle(): string
    {
        return 'Отказаться';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonColorClass(): string
    {
        return 'refusal';
    }

    /**
     * Метод проверяет, имеет ли пользователь право отказаться от выполнения задания.
     * @param Task $task
     * @return bool
     */
    public static function getUserRightsCheck(Task $task): bool
    {
        return \Yii::$app->user->id === $task->executor_id && $task->status == Task::STATUS_IN_PROGRESS;
    }
}
