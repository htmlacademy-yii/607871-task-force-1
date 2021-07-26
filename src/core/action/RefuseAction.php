<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

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

    public function getUserRightsCheck(int $clientId, $executorId, int $userId): bool
    {
        return $userId === $executorId;
    }
}
