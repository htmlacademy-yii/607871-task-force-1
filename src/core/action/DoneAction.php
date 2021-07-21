<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class DoneAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'done';
    }

    public function getActionTitle(): string
    {
        return 'Выполнено';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId): bool
    {
        return $userId === $clientId;
    }
}
