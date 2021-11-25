<?php

use yii\widgets\ActiveForm;
use frontend\models\City;
use \yii\helpers\Html;

?>
<section class="registration__user">
    <h1>Регистрация аккаунта</h1>
    <div class="registration-wrapper">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'post',
            'options' => ['class' => 'registration__user-form form-create'],
            'fieldConfig' => [
                'enableAjaxValidation' => false,
                'template' => "{label}\n{input}\n{error}",
                'inputOptions' => ['class' => 'input textarea'],
                'errorOptions' => ['tag' => 'span', 'class' => 'registration__text-error'],
                'options' => [
                    'tag' => 'div',
                    'class' => 'field-container field-container--registration',
                ]],

        ]); ?>

        <?= $form->field($user, 'email')->textInput(['placeholder' => 'kumarm@mail.ru']); ?>
        <?= $form->field($user, 'name')->textInput(['placeholder' => 'Мамедов Кумар']); ?>

        <?= $form->field($profile, 'city_id')->dropDownList(City::getCityMap(), [
            'class' => 'multiple-select input town-select registration-town',
            'prompt' => ''
        ]) ?>

        <?= $form->field($user, 'password')->passwordInput(); ?>
        <?= Html::submitButton('Создать аккаунт', ['class' => "button button__registration"]) ?>
        <? ActiveForm::end(); ?>
    </div>
</section>
