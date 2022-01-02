<?php

namespace App\business;

use App\core\action\CancelAction;
use App\core\action\DoneAction;
use App\core\action\RefuseAction;
use App\core\action\VolunteerAction;
use App\core\TaskActionTemplate;
use App\Exception\DataException;

class Task
{
    const STATUS_NEW = 'new';
    const STATUS_CANCELED = 'canceled';
    const STATUS_IN_PROGRESS = 'in progress';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

    protected $clientId;
    protected $executorId;


    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }
            $this->{$key} = $value;
        }
    }

    public static function getStatusMapping(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCELED => 'Отменено',
            self::STATUS_IN_PROGRESS => 'В работе',
            self::STATUS_FAILED => 'Провалено',
            self::STATUS_FINISHED => 'Выполнено',
        ];
    }

    public static function getStatusByAction(string $action): string
    {
        $actionStatusMap = [
            (new CancelAction())->getActionCode() => self::STATUS_CANCELED,
            (new DoneAction())->getActionCode() => self::STATUS_FINISHED,
            (new RefuseAction())->getActionCode() => self::STATUS_FAILED,
            (new VolunteerAction())->getActionCode() => self::STATUS_IN_PROGRESS,
        ];

        if (!isset($actionStatusMap[$action])) {
            throw new DataException("Для действия $action не назначен соответствующий статус задания");
        }

        return $actionStatusMap[$action];
    }

    public static function getPossibleActions(string $status, int $clientId, $executorId, $userId): array
    {
        $actionStatusMap = [
            self::STATUS_NEW => [new CancelAction(), new VolunteerAction()],
            self::STATUS_IN_PROGRESS => [new DoneAction(), new RefuseAction()],
        ];

        if (!isset($actionStatusMap[$status])) {
            throw new DataException("Для статуса задания $status нет подходящих действий");
        }

        return array_values(array_filter($actionStatusMap[$status], function(TaskActionTemplate $action) use ($clientId, $executorId, $userId) {
           return $action->getUserRightsCheck($clientId, $executorId, $userId);
        }));
    }

}
