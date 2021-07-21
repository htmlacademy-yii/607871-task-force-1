<?php


namespace App\core\action;


use App\core\TaskActionTemplate;

class CancelAction extends TaskActionTemplate
{

    public function getActionCode(): string
    {
        return 'cancel';
    }

    public function getActionTitle(): string
    {
        return 'Отменить';
    }

    public function getUserRightsCheck(int $clientId, $executorId, int $userId): bool
    {
        return $userId === $clientId;
    }
}
