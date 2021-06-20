<?php

class Task
{
    const STATUS_NEW = 'new';
    const STATUS_CANCELED = 'canceled';
    const STATUS_IN_PROGRESS = 'in progress';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';

    const ACTION_CANCEL = 'cancel';
    const ACTION_DONE = 'done';
    const ACTION_VOLUNTEER = 'volunteer';
    const ACTION_REFUSE = 'refuse';


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

    public static function getActionMapping(): array
    {
        return [
            self::ACTION_VOLUNTEER => 'Откликнуться',
            self::ACTION_CANCEL => 'Отменить',
            self::ACTION_DONE => 'Выполнено',
            self::ACTION_REFUSE => 'Отказаться',
        ];
    }

    public static function getStatusByAction(string $action): ?string
    {
        $actionStatusMap = [
            self::ACTION_CANCEL => self::STATUS_CANCELED,
            self::ACTION_DONE => self::STATUS_FINISHED,
            self::ACTION_REFUSE => self::STATUS_FAILED,
            self::ACTION_VOLUNTEER => self::STATUS_IN_PROGRESS,
        ];

        return $actionStatusMap[$action] ?? null;
    }

    public static function getPossibleActions(string $status): ?array
    {
        $actionStatusMap = [
            self::STATUS_NEW => [self::ACTION_CANCEL, self::ACTION_VOLUNTEER ],
            self::STATUS_IN_PROGRESS => [self::ACTION_DONE, self::ACTION_REFUSE],
        ];

        return $actionStatusMap[$status] ?? null;
    }

}
