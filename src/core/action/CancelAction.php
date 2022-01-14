<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;

class CancelAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'cancel';
    }

    public function getActionTitle(): string
    {
        return 'Отменить';
    }

    public function getButtonColorClass(): string
    {
        return 'cancel';
    }

    public function getUserRightsCheck(Task $task): bool
    {
        return \Yii::$app->user->id === $task->client_id;
    }
}
