<?php


namespace frontend\models\forms;


use frontend\models\User;
use frontend\service\UserService;
use yii\base\Model;


class LoginForm extends Model
{
    public $email;
    public $password;
    private $_user;

    /**
     * {@inheritdoc}
     */
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
            ['password', 'validateUser'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'email' => 'email',
            'password' => 'Пароль',
        ];
    }

    /**
     * Метод отвечает за проверку данных пользователя, пытающегося залогиниться.
     */
    public function validateUser()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user ||  !UserService::validatePassword($user, $this->password)) {
                $this->addError('password', 'Неправильный email или пароль!');
            }
        }
    }

    /**
     * Метод проверяет, существует ли пользователь с указанным email.
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email]);
        }

        return $this->_user;
    }
}