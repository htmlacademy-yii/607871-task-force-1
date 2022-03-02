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
    const TYPE_TASK_RECALLED = 5;

    const TYPE_MESSAGE_MAP = [
        self::TYPE_NEW_MESSAGE => 'Новое сообщение в чате',
        self::TYPE_TASK_CONFIRMED => 'Выбран исполнитель для',
        self::TYPE_TASK_CLOSED => 'Завершено задание',
        self::TYPE_TASK_FAILED => 'Провалено задание',
        self::TYPE_TASK_RECALLED => 'Получен отзыв по заданию',
    ];

    const CSS_ICON_CLASS_MAP = [
        self::TYPE_NEW_MESSAGE => 'lightbulb__new-task--message',
        self::TYPE_TASK_CONFIRMED => 'lightbulb__new-task--executor',
        self::TYPE_TASK_CLOSED => 'lightbulb__new-task--close',
        self::TYPE_TASK_FAILED => 'lightbulb__new-task--close',
        self::TYPE_TASK_RECALLED => 'lightbulb__new-task--close',
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
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
