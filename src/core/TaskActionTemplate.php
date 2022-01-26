<?php


namespace App\core;


use frontend\models\Task;

abstract class TaskActionTemplate
{
    abstract public function getActionCode(): string;
    abstract public function getActionTitle(): string;
    abstract public function getButtonColorClass(): string;
    abstract public static function getUserRightsCheck(Task $task): bool;
}
