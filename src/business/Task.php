<?php

namespace App\business;

use App\core\action\CancelAction;
use App\core\action\DoneAction;
use App\core\action\RefuseAction;
use App\core\action\VolunteerAction;
use App\core\TaskActionTemplate;

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

    public static function getStatusByAction(string $action): ?string
    {
        $actionStatusMap = [
            (new CancelAction())->getActionCode() => self::STATUS_CANCELED,
            (new DoneAction())->getActionCode() => self::STATUS_FINISHED,
            (new RefuseAction())->getActionCode() => self::STATUS_FAILED,
            (new VolunteerAction())->getActionCode() => self::STATUS_IN_PROGRESS,
        ];

        return $actionStatusMap[$action] ?? null;
    }

    public static function getPossibleActions(string $status, $clientId, $executorId, $userId): array
    {
        $actionStatusMap = [
            self::STATUS_NEW => [new CancelAction(), new VolunteerAction()],
            self::STATUS_IN_PROGRESS => [new DoneAction(), new RefuseAction()],
        ];

        if (!isset($actionStatusMap[$status])) {
            return [];
        }

        return array_values(array_filter($actionStatusMap[$status], function(TaskActionTemplate $action) use ($clientId, $executorId, $userId) {
           return $action->getUserRightsCheck($clientId, $executorId, $userId);
        }));
    }

}
