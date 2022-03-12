<?php


namespace App\core;


use frontend\models\Task;

abstract class TaskActionTemplate
{
    /**
     * Метод возвращает код действия по заданию.
     * @return string
     */
    abstract public function getActionCode(): string;

    /**
     * Метод возвращает подпись к кнопке, отвеающей за действие над заданием.
     * @return string
     */
    abstract public function getActionTitle(): string;

    /**
     * Метод возвращает CSS-класс для определения цвета кнопки
     * @return string
     */
    abstract public function getButtonColorClass(): string;

    /**
     * Метод проверяет права текущего пользователя по заданию.
     * @param Task $task
     * @return bool
     */
    abstract public static function getUserRightsCheck(Task $task): bool;
}
