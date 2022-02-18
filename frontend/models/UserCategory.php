<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_category".
 *
 * @property int $user_id
 * @property int $category_id
 * @property int $active
 *
 * @property Category $category
 * @property User $user
 */
class UserCategory extends \yii\db\ActiveRecord
{
    const USER_CATEGORY_ACTIVE_SET = 1;
    const USER_CATEGORY_ACTIVE_UNSET = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'active'], 'safe'],
            [['user_id', 'category_id', 'active'], 'required'],
            [['user_id', 'category_id', 'active'], 'integer'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'category_id' => 'Category ID',
            'active' => 'Active',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
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
