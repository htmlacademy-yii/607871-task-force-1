<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class DoneAction extends TaskActionTemplate
{

    public function getAvailableAction()
    {
        return 'done';
    }

    public function getActionTitle()
    {
        return 'Выполнено';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId)
    {
        return ($userId === $clientId);
    }
}
