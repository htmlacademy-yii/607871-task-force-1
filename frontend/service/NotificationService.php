<?php


namespace frontend\service;


use frontend\models\Task;
use frontend\models\User;
use frontend\models\UserMessage;
use yii\web\TooManyRequestsHttpException;

class NotificationService
{
    public Task $task;
    public User $user;

    const MESSAGE_TYPE_EMAIL_MAP = [
        UserMessage::TYPE_NEW_MESSAGE => 'taskCorrespondence-html',
        UserMessage::TYPE_TASK_CONFIRMED => 'taskConfirmed-html',
        UserMessage::TYPE_TASK_CLOSED => 'taskClosed-html',
        UserMessage::TYPE_TASK_FAILED => 'taskFailed-html',
        UserMessage::TYPE_TASK_RECALL => 'taskRecalled-html',
        UserMessage::TYPE_TASK_RESPONDED => 'taskResponded-html',
    ];

    const TYPE_MESSAGE_MAP = [
        UserMessage::TYPE_NEW_MESSAGE => 'Новое сообщение в чате',
        UserMessage::TYPE_TASK_CONFIRMED => 'Выбран исполнитель для',
        UserMessage::TYPE_TASK_CLOSED => 'Завершено задание',
        UserMessage::TYPE_TASK_FAILED => 'Провалено задание',
        UserMessage::TYPE_TASK_RECALL => 'Получен отзыв',
        UserMessage::TYPE_TASK_RESPONDED => 'Получен отклик по заданию'
    ];

    const TASK_ACTIONS = [
        UserMessage::TYPE_TASK_CONFIRMED,
        UserMessage::TYPE_TASK_CLOSED,
        UserMessage::TYPE_TASK_FAILED,
        UserMessage::TYPE_TASK_RESPONDED,
    ];

    public function __construct(Task $task, User $user)
    {
        $this->user = $user;
        $this->task = $task;
    }

    /**
     * Метод создает сообщение для пользователя в базе данных и отправляет уведомление на электронную почту.
     * @param $messageType
     * @return bool
     */
    public function inform($messageType)
    {
        if (!$this->user->userSettings || !$this->checkUserSettings($messageType)) {
            return false;
        }

        $emailTemplate = self::MESSAGE_TYPE_EMAIL_MAP[$messageType];
        $this->createUserMessage($messageType);

        $this->sendEmail($emailTemplate, $messageType);
        return true;
    }

    /**
     * Метод создает сообщение для пользователя в базе данных.
     * @param $messageType
     * @return bool
     */
    protected function createUserMessage($messageType)
    {
        $message = new UserMessage([
            'user_id' => $this->user->id,
            'task_id' => $this->task->id,
            'type' => $messageType,
        ]);
        return $message->save();
    }

    /**
     * Метод отправляет сообщение для пользователя на элекетронную почту.
     * @param string $template
     * @param int $messageType
     * @return bool
     */
    protected function sendEmail(string $template, int $messageType): bool
    {
        $message = \Yii::$app->mailer->compose($template, [
            'user' => $this->user,
            'task' => $this->task,
        ]);
        $message->setTo($this->user->email)->setFrom('yii-taskforce@mail.ru')
            ->setSubject(self::TYPE_MESSAGE_MAP[$messageType] . ' "' . $this->task->title . '"');

        return $message->send();
    }

    /**
     * Метод проверяет, настроено ли у пользователя разрешение на уведомление конкретного типа.
     * @param int $messageType
     * @return bool
     */
    protected function checkUserSettings(int $messageType)
    {
        if ($this->user->userSettings->task_actions) {
            if (in_array($messageType, self::TASK_ACTIONS)) {
                return true;
            }
        }

        if ($this->user->userSettings->new_message && $messageType === UserMessage::TYPE_NEW_MESSAGE) return true;
        if ($this->user->userSettings->new_recall && $messageType === UserMessage::TYPE_TASK_RECALL) return true;
        return false;
    }
}