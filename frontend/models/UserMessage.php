<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "message".
 *
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property int $type
 * @property string $creation_date
 *
 * @property Task $task
 * @property User $user
 */
class UserMessage extends \yii\db\ActiveRecord
{
    const TYPE_NEW_MESSAGE = 1;
    const TYPE_TASK_CONFIRMED = 2;
    const TYPE_TASK_CLOSED = 3;
    const TYPE_TASK_FAILED = 4;
    const TYPE_TASK_RESPONDED = 5;

    const CSS_ICON_CLASS_MAP = [
        self::TYPE_NEW_MESSAGE => 'lightbulb__new-task--message',
        self::TYPE_TASK_CONFIRMED => 'lightbulb__new-task--executor',
        self::TYPE_TASK_CLOSED => 'lightbulb__new-task--close',
        self::TYPE_TASK_FAILED => 'lightbulb__new-task--close',
        self::TYPE_TASK_RESPONDED => 'lightbulb__new-task--close',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'user_id', 'type'], 'required'],
            [['task_id', 'user_id', 'type'], 'integer'],
            [['creation_date'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['task_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'type' => 'Type',
            'creation_date' => 'Creation Date',
        ];
    }

    /**
     * Метод возвращает задание, по которому было сгенерировано сообщение для пользователя.
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Метод возвращает пользователя, для которого было создано сообщение.
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
