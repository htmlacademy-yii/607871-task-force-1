<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class DoneAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'complete';
    }

    public function getActionTitle(): string
    {
        return 'Выполнено';
    }

    public function getButtonColorClass(): string
    {
        return 'request';
    }

    public function getUserRightsCheck(int $clientId, $executorId, $userId): bool
    {
        return $userId === $clientId;
    }
}
