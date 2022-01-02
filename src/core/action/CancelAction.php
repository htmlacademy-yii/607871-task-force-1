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

    public function getButtonColorClass(): string
    {
        return 'cancel';
    }

    public function getUserRightsCheck(int $clientId, $executorId, $userId): bool
    {
        return $userId === $clientId;
    }
}
