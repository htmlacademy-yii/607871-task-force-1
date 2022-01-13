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

    public function getButtonColorClass(): string
    {
        return 'refusal';
    }

    public function getUserRightsCheck(int $clientId, $executorId, $userId): bool
    {
        return $userId === $executorId;
    }
}
