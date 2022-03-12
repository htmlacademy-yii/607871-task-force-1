<?php


namespace frontend\widgets;


use frontend\models\forms\LoginForm;
use yii\base\Widget;

class LoginDialogWidget extends Widget
{
    /**
     * Виджет отображает форму "Вход", чтобы пользователь мог залогиниться.
     * @return string
     */
    public function run()
    {
        $loginForm = new LoginForm();
        return $this->render('login-form-widget', ['loginForm' => $loginForm]);
    }
}