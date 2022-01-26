<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class DoneAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'complete';
    }

    public function getActionTitle(): string
    {
        return 'Завершить';
    }

    public function getButtonColorClass(): string
    {
        return 'request';
    }

    public static function getUserRightsCheck(Task $task): bool
    {
        return \Yii::$app->user->id === $task->client_id && $task->status == Task::STATUS_IN_PROGRESS;
    }
}
