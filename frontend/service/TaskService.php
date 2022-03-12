<?php


namespace frontend\service;

use App\Exception\DataException;
use frontend\models\Respond;
use frontend\models\Task;
use frontend\models\User;


class TaskService
{
    const BUSINESS_STATUS_MAP = [
        Task::STATUS_NEW => \App\business\Task::STATUS_NEW,
        Task::STATUS_CANCELED => \App\business\Task::STATUS_CANCELED,
        Task::STATUS_IN_PROGRESS => \App\business\Task::STATUS_IN_PROGRESS,
        Task::STATUS_FINISHED => \App\business\Task::STATUS_FINISHED,
        Task::STATUS_FAILED => \App\business\Task::STATUS_FAILED
    ];

    /**
     * На основании ответа, полученного от геокодера API Яндекс.Карт метод собирает треубемый массив данных для последующего
     * возвращения на фронтэнд.
     * @param array $responseData
     * @return array
     */
    public static function createAutocompleteAddress(array $responseData)
    {
        $GeoObjects = $responseData['response']['GeoObjectCollection']['featureMember'];
        $result = [];

        foreach ($GeoObjects as $value) {
            try {
                $yandexGeo = new YandexGeo();
                $yandexGeo->setParameters($value['GeoObject']);
            } catch (DataException $e) {
                continue;
            }

            if (\Yii::$app->user->identity->profile->city->name === $yandexGeo->city) {
                $result [] = $yandexGeo->getAttributes();
            } else {
                continue;
            }
        }
        return $result;
    }

    /**
     * Метод проверяет, откликался ли уже на данное задание конкретный пользователь.
     * @param Task $task
     * @param int $userId
     * @return bool
     */
    public static function isVolunteer(Task $task, int $userId): bool
    {
        return isset($userId) ? !!$task->getResponds()->andWhere(['respond.user_id' => $userId])->count() : false;
    }

    /**
     * Метод возвращает человекопонятное название статуса задания на основании кода статуса задания в базе данных.
     * @param Task $task
     * @return mixed
     */
    public static function getBusinessStatus(Task $task)
    {
        return self::BUSINESS_STATUS_MAP[$task->status];
    }

    /**
     * Метод определяет, карточку какого пользователя нужно отображаеть в правом верхнему углу задания - заказчика или исполнителя.
     * @param Task $task
     * @return User
     */
    public static function chooseUser(Task $task): User
    {
        $user = $task->client;
        if (isset($task->executor_id) && \Yii::$app->user->identity->id === $task->client_id) {
            $user = $task->executor;
        }
        return $user;
    }

    /**
     * Метод отвечает за назначение пользователя в качестве исполнителя по заданию и соответствующую смену статуса задания.
     * @param Task $task
     * @param Respond $respond
     * @param User $executor
     * @return bool
     */
    public static function executorConfirm(Task $task, Respond $respond, User $executor)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $task->status = Task::STATUS_IN_PROGRESS;
            $task->executor_id = $executor->id;
            $respond->status = Respond::STATUS_CONFIRMED;
            $task->save();
            $respond->save();
            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }
}