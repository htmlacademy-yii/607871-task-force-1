<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_portfolio".
 *
 * @property int $id
 * @property int $user_id
 * @property string $file
 *
 * @property User $user
 */
class UserPortfolio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_portfolio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'file'], 'required'],
            [['user_id'], 'integer'],
            [['file'], 'string', 'max' => 255],
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
            'user_id' => 'User ID',
            'file' => 'File',
        ];
    }

    /**
     * Метод возвращает пользователя, которому принадлежит портфолио (примеры работ).
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
