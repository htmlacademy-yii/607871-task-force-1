<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class VolunteerAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'volunteer';
    }

    public function getActionTitle(): string
    {
        return 'Откликнуться';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId): bool
    {
        return ($userId !== $clientId && $executorId === null);

    }
}
