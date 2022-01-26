<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class VolunteerAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'response';
    }

    public function getActionTitle(): string
    {
        return 'Откликнуться';
    }

    public function getButtonColorClass(): string
    {
        return 'response';
    }

    public static function getUserRightsCheck(Task $task): bool
    {
        return (\Yii::$app->user->id !== $task->client_id && $task->executor_id === null && !$task->isVolunteer(\Yii::$app->user->id));

    }
}
