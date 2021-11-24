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
            [['task_id', 'description', 'rating'], 'required'],
            [['task_id', 'rating'], 'integer'],
            [['description'], 'string'],
            [['creation_date'], 'safe'],
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
            'description' => 'Description',
            'rating' => 'Rating',
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
        return $this->hasOne(Task::class, ['id' => 'task_id'])->with('client');
    }

    public function getReviewer()
    {
        return $this->hasOne(User::class, ['id' => 'client_id'])
            ->viaTable('task', ['id' => 'task_id'])
            ->with('profile');
    }

}
