<?php

use yii\helpers\Html;
use \yii\widgets\ActiveForm;
use \yii\helpers\Url;

/**
 * @var \frontend\models\forms\LoginForm $loginForm
 */

?>
<section class="modal enter-form form-modal" id="enter-form">
    <h2>Вход на сайт</h2>
    <?php $form = ActiveForm::begin([
        'action' => ['login'],
        'method' => 'post',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-modal-description'],
            'inputOptions' => ['class' => 'enter-form-email input input-middle'],
            'errorOptions' => ['tag' => 'span'],
            'options' => [
                'tag' => 'p',
            ]],
        'enableAjaxValidation' => true,
        'enableClientValidation' => true,
    ]); ?>

    <?= yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['site/auth'],
        'popupMode' => false,
    ]); ?>
    <?= $form->field($loginForm, 'email')->textInput(); ?>
    <?= $form->field($loginForm, 'password')->passwordInput(); ?>

    <?= Html::submitButton('Войти', ['class' => 'button']); ?>
    <?php ActiveForm::end(); ?>
    <button class="form-modal-close"  id="close-modal" type="button">Закрыть</button>
</section>
