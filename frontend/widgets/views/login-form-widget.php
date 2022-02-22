<?php

use yii\helpers\Html;
use \yii\widgets\ActiveForm;
use \yii\helpers\Url;

/**
 * @var \frontend\models\forms\LoginForm $model
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
   <!-- <a href="<?/*= Url::to(['/loginvk']); */?>" rel ="nofollow" title="Войти через Вконтакте" class="modal__social-vk">
<img src="/img/vk-icon.svg" width="60" height="60" alt="Вконтакте">
    </a>-->
    <?= yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['main/auth'],
        'popupMode' => false,
    ]); ?>
    <?= $form->field($model, 'email')->textInput(); ?>
    <?= $form->field($model, 'password')->passwordInput(); ?>

    <?= Html::submitButton('Войти', ['class' => 'button']); ?>
    <?php ActiveForm::end(); ?>
    <button class="form-modal-close"  id="close-modal" type="button">Закрыть</button>
</section>
