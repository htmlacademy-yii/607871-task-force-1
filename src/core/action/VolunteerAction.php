<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

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

    public function getUserRightsCheck(int $clientId, $executorId, $userId): bool
    {
        return ($userId !== $clientId && $executorId === null);

    }
}
