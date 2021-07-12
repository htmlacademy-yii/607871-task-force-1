<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class CancelAction extends TaskActionTemplate
{

    public function getAvailableAction()
    {
        return 'cancel';
    }

    public function getActionTitle()
    {
        return 'Отменить';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId)
    {
        return ($userId === $clientId);
    }
}
