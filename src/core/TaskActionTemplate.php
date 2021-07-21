<?php


namespace App\core;


abstract class TaskActionTemplate
{
    abstract public function getActionCode(): string;
    abstract public function getActionTitle(): string;
    abstract public function getUserRightsCheck(int $clientId, $executorId, int $userId): bool;
}
