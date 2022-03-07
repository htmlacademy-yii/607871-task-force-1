<?php


namespace frontend\models\forms;


use frontend\models\User;
use yii\base\Model;


class LoginForm extends Model
{
    public $email;
    public $password;


    private $_user;

    public function rules()
    {
        return [
            [['email', 'password'], 'required', 'message' => 'Поле должно быть заполнено'],
            [['email', 'password'], 'safe'],
            [['email', 'password'], 'trim'],
            ['email', 'email', 'message' => 'Введите валидный адрес электронной почты'],
            ['email', 'string', 'max' => 50, 'message' => 'Не больше 50 символов'],
            ['email', 'exist', 'skipOnError' => false, 'targetClass' => User::class,
                'targetAttribute' => 'email', 'message' => 'Пользвателя с таким email не существует'],
            [['password'], 'string', 'min' => 8, 'max' => 64, 'tooShort' => "Длина пароля от {min} символов", 'tooLong' => 'Длина пароля до {max} символов'],
            ['password', 'validatePassword'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'email',
            'password' => 'Пароль',
        ];
    }

    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user ||  !$user->validatePassword($this->password)) {
                $this->addError('password', 'Неправильный email или пароль!');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}