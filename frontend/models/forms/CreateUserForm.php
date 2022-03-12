<?php


namespace frontend\models\forms;

use frontend\models\City;
use frontend\models\Profile;
use frontend\models\User;

class CreateUserForm extends BaseModelForm
{
    public $name;
    public $email;
    public $password;
    public $city_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'name', 'password'], 'safe'],
            [['email', 'name', 'password'], 'trim'],
            [['email', 'password', 'city_id'], 'required', 'message' => 'Поле должно быть заполнено'],
            ['name', 'required', 'message' => 'Введите ваше имя и фамилию'],
            [['name', 'email'], 'string', 'min' => 5, 'max' => 50, 'tooShort' => "Не меньше {min} символов", 'tooLong' => 'Не больше {max} символов'],
            [['name', 'email'], 'isUserAttributeUnique'],
            ['email', 'email', 'message' => 'Введите валидный адрес электронной почты'],
            [['city_id'], 'exist', 'skipOnError' => false, 'targetClass' => City::class, 'targetAttribute' => ['city_id' => 'id'], 'message' => 'Укажите город, чтобы находить подходящие задачи'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'Электронная почта',
            'name' => 'Ваше имя',
            'city_id' => 'Город',
            'password' => 'Пароль',
        ];
    }

    /**
     * Метод "заливает" данные из свойств формы в свойства моделей User и Profile и сохраняет их в базе данных.
     * @return bool
     * @throws \yii\base\Exception
     */
    public function saveFields(): bool
    {
        $user = new User(['scenario' => User::SCENARIO_CREATE_USER]);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->password_hash = \Yii::$app->security->generatePasswordHash($this->password);
        $profile = new Profile();
        $profile->city_id = $this->city_id;
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $user->save();
            $profile->user_id = $user->primaryKey;
            $profile->save();
            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return false;
        }
    }
}