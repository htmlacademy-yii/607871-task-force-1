<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "recall".
 *
 * @property int $id
 * @property int $task_id
 * @property string $description
 * @property int $rating
 * @property string $creation_date
 * @property int $taskStatus
 *
 * @property Task $tasks
 */
class Recall extends \yii\db\ActiveRecord
{
    public $status;
    const COMPLETION_YES = 'yes';
    const COMPLETION_PROBLEMS = 'difficult';

    const COMPLETION = [
        self::COMPLETION_YES => 'да',
        self::COMPLETION_PROBLEMS => 'Возникли проблемы'
    ];

    const TASK_STATUS_MAP = [
        self::COMPLETION_YES => Task::STATUS_FINISHED,
        self::COMPLETION_PROBLEMS => Task::STATUS_FAILED
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'recall';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'task_id', 'description', 'rating'], 'safe'],
            [['status', 'description', 'rating'], 'required', 'message' => 'Поле должно быть заполнено'],
            ['description', 'trim'],
            [['status', 'description'], 'string'],
            ['rating', 'integer', 'min' => 1, 'max' => 5, 'message' => "Поставьте оценку от 1 до 5"],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'description' => 'Комментарий',
            'rating' => 'Оценка',
            'creation_date' => 'Creation Date',
        ];
    }

    /**
     * Метод возвращает задание, по которому был оставлен конкретный отзыв.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id'])->with('client');
    }

    /**
     * Метод возвращает пользователя, который оставил отзыв по заданию, и его профиль.
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getReviewer()
    {
        return $this->hasOne(User::class, ['id' => 'client_id'])
            ->viaTable('task', ['id' => 'task_id'])
            ->with('profile');
    }

    /**
     * В зависимости от того, с каким признаком клиент завершает задание ("Да" или "Возникли проблемы"),
     * данный метод выбирает статус, который будет присвоен заданию ("Выполнено" или "Провалено").
     * @return mixed
     */

    public function getTaskStatus()
    {
        return self::TASK_STATUS_MAP[$this->status];
    }
}
