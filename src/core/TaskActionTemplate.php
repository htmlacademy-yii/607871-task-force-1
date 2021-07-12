<?php


namespace App\core;


abstract class TaskActionTemplate
{
    abstract public function getAvailableAction();
    abstract public function getActionTitle();
    abstract public function getUserRightsCheck(int $clientId, $executorId, int $userId);
}
