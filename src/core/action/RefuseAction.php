<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class RefuseAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'refuse';
    }

    public function getActionTitle(): string
    {
        return 'Отказаться';
    }

    public function getButtonColorClass(): string
    {
        return 'refusal';
    }

    public static function getUserRightsCheck(Task $task): bool
    {
        return \Yii::$app->user->id === $task->executor_id && $task->status == Task::STATUS_IN_PROGRESS;
    }
}
