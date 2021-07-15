<?php


namespace App\core;


abstract class TaskActionTemplate
{
    abstract public function getActionCode();
    abstract public function getActionTitle();
    abstract public function getUserRightsCheck(int $clientId, $executorId, int $userId);
}
