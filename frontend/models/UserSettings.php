<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_settings".
 *
 * @property int $user_id
 * @property int $new_message
 * @property int $task_actions
 * @property int $new_recall
 * @property int $hide_profile
 * @property int $contacts_only_for_client
 *
 * @property User $user
 */
class UserSettings extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['new_message', 'task_actions', 'new_recall', 'hide_profile', 'contacts_only_for_client'], 'safe'],
            [['new_message', 'task_actions', 'new_recall', 'hide_profile', 'contacts_only_for_client'], 'integer', 'min' => 0, 'max' => 1],
            ['user_id', 'integer'],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'new_message' => 'Новое сообщение',
            'task_actions' => 'Действия по заданию',
            'new_recall' => 'Новый отзыв',
            'hide_profile' => 'Не показывать мой профиль',
            'contacts_only_for_client' => 'Показывать мои контакты только заказчику',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userSettings');
    }

    public function deactivateAll()
    {
        $this->new_recall = 0;
        $this->task_actions = 0;
        $this->new_message = 0;
        $this->contacts_only_for_client = 0;
        $this->hide_profile = 0;
    }
}
