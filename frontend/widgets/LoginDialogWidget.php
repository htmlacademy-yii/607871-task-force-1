<?php


namespace frontend\widgets;


use frontend\models\forms\LoginForm;
use yii\base\Widget;

class LoginDialogWidget extends Widget
{
    public function run()
    {
        $model = new LoginForm();

        return $this->render('login-form-widget', ['model' => $model]);
    }
}