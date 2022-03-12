<?php


namespace App\core\action;


use App\core\TaskActionTemplate;
use frontend\models\Task;
use frontend\models\User;
use frontend\service\TaskService;
use frontend\service\UserService;

class VolunteerAction extends TaskActionTemplate
{
    /**
     * {@inheritdoc}
     */
    public function getActionCode(): string
    {
        return 'response';
    }

    /**
     * {@inheritdoc}
     */
    public function getActionTitle(): string
    {
        return 'Откликнуться';
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonColorClass(): string
    {
        return 'response';
    }

    /**
     * Метод проверяет, имеет ли пользователь право откликнуться на задание.
     * @param Task $task
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public static function getUserRightsCheck(Task $task): bool
    {
        $isVolunteer = TaskService::isVolunteer($task, \Yii::$app->user->id);
        $user = User::findOne(\Yii::$app->user->id);

        return ($user->id !== $task->client_id && $task->executor_id === null && !$isVolunteer && $user->categories);
    }
}
