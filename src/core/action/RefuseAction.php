<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class RefuseAction extends TaskActionTemplate
{

    public function getActionCode()
    {
        return 'refuse';

    }

    public function getActionTitle()
    {
        return 'Отказаться';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId)
    {
        return $userId === $executorId;
    }
}
