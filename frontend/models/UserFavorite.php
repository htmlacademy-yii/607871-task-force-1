<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_favorite".
 *
 * @property int $id
 * @property int $chooser_id
 * @property int $chosen_id
 * @property int $active
 *
 * @property User $chooser
 * @property User $chosen
 */
class UserFavorite extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chooser_id', 'chosen_id'], 'required'],
            [['chooser_id', 'chosen_id', 'active'], 'integer'],
            [['chooser_id', 'chosen_id'], 'unique', 'targetAttribute' => ['chooser_id', 'chosen_id']],
            [['chooser_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['chooser_id' => 'id']],
            [['chosen_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['chosen_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chooser_id' => 'Chooser ID',
            'chosen_id' => 'Chosen ID',
            'active' => 'Active',
        ];
    }

    /**
     * Метод возвращает пользователя, который добавил в избранное конкретного потенциального испольнителя.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChooser()
    {
        return $this->hasOne(User::class, ['id' => 'chooser_id']);
    }

    /**
     * Метод возвращает пользователя, которого добавили в избранное как потенциального исполнителя.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChosen()
    {
        return $this->hasOne(User::class, ['id' => 'chosen_id']);
    }
}
