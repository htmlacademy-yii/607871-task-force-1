<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class VolunteerAction extends TaskActionTemplate
{

    public function getAvailableAction()
    {
        return 'volunteer';
    }

    public function getActionTitle()
    {
        return 'Откликнуться';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId)
    {
        return ($userId !== $clientId && $executorId === null);

    }
}
