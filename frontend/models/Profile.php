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
 * @property string|null $telegram
 *
 * @property City $city
 * @property User $user
 */
class Profile extends \yii\db\ActiveRecord
{
    const SCENARIO_ACCOUNT_INPUT_RULES = 'account_input_rules';

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
            [['description', 'birth_date', 'avatar', 'city_id', 'phone', 'skype', 'telegram'], 'safe'],
            ['birth_date', 'datetime', 'format' => 'yyyy-MM-dd',
                'max' => date('Y-m-d', strtotime('-14 years', time())), 'strictDateFormat' => true,
                'on' => Profile::SCENARIO_DEFAULT, 'message' => 'Введите дату в формате ГГГГ-ММ-ДД'],
            ['birth_date', 'datetime', 'format' => 'dd.MM.yyyy',
                'max' => date('d.m.Y', strtotime('-14 years', time())), 'strictDateFormat' => true,
                'on' => Profile::SCENARIO_ACCOUNT_INPUT_RULES, 'message' => 'Введите дату в формате ДД.ММ.ГГГГ'],
            [['user_id', 'city_id'], 'integer'],
            ['city_id', 'required'],
            [['description'], 'string'],
            [['avatar'], 'string', 'max' => 255],
            [['phone'], 'match', 'pattern' => '/^[\d]{11}/i', 'message' => 'Указан неверный номер телефона'],
            [['skype', 'telegram'], 'string', 'max' => 50],
            [['phone'], 'unique', 'message' => 'Пользователь с таким номером телефона уже существует'],
            [['skype'], 'unique', 'message' => 'Пользователь с таким Skype уже существует'],
            [['telegram'], 'unique', 'message' => 'Пользователь с таким Skype уже существует'],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => false, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id'], 'message' => 'Укажите город, чтобы находить подходящие задачи'],
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
            'birth_date' => 'День рождения',
            'description' => 'Информация о себе',
            'avatar' => 'Avatar',
            'city_id' => 'Город',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'skype' => 'Skype',
            'telegram' => 'Телеграм',
        ];
    }

    /**
     * Метод возвращает город, к которому привязан профиль пользователя.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(City::class, ['id' => 'city_id']);
    }

    /**
     * Метод возвращает пользователя, к которому относится конкретный пользовательский профиль.
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('profile');
    }

}
