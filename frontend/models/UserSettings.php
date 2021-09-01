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
            [['user_id'], 'required'],
            [['user_id', 'new_message', 'task_actions', 'new_recall', 'hide_profile', 'contacts_only_for_client'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'new_message' => 'New Message',
            'task_actions' => 'Task Actions',
            'new_recall' => 'New Recall',
            'hide_profile' => 'Hide Profile',
            'contacts_only_for_client' => 'Contacts Only For Client',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
