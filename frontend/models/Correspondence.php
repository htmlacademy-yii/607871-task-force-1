<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "correspondence".
 *
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property string $message
 * @property string $published_at
 *
 * @property Task $task
 * @property User $user
 */
class Correspondence extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'correspondence';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'user_id', 'message'], 'required'],
            [['task_id', 'user_id'], 'integer'],
            [['message'], 'string'],
            [['published_at', 'message', 'task_id', 'user_id'], 'safe'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'message' => 'Message',
            'published_at' => 'PUBLISHED AT',
        ];
    }

    /**
     * Метод возвращает задание, к которму относится сообщение в блоке "Переписка".
     * @return \yii\db\ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id']);
    }

    /**
     * Метод возвращает пользователя, создавшего сообщение в блоке "Переписка".
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
