<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $birth_date
 * @property string|null $description
 * @property string|null $avatar
 * @property int $city_id
 * @property string|null $address
 * @property string|null $phone
 * @property string|null $skype
 * @property string|null $other
 *
 * @property City $city
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'birth_date', 'city_id'], 'required'],
            [['user_id', 'city_id'], 'integer'],
            [['birth_date'], 'safe'],
            [['description'], 'string'],
            [['avatar'], 'string', 'max' => 100],
            [['address'], 'string', 'max' => 255],
            [['phone'], 'string', 'max' => 12],
            [['skype', 'other'], 'string', 'max' => 50],
            [['phone'], 'unique'],
            [['skype'], 'unique'],
            [['other'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id']],
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
            'birth_date' => 'Birth Date',
            'description' => 'Description',
            'avatar' => 'Avatar',
            'city_id' => 'City ID',
            'address' => 'Address',
            'phone' => 'Phone',
            'skype' => 'Skype',
            'other' => 'Other',
        ];
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
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
