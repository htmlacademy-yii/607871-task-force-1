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
 *
 * @property Task $tasks
 */
class Recall extends \yii\db\ActiveRecord
{
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
            [['task_id', 'description', 'rating'], 'safe'],
            [['description', 'rating'], 'required', 'message' => 'Поле должно быть заполнено'],
            ['description', 'trim'],
            ['description', 'string'],
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
}
