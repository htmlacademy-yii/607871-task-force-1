<?php

use \yii\helpers\Html;

use yii\bootstrap4\ActiveForm;
use \frontend\models\Category;
use \frontend\models\City;
use \frontend\assets\AccountAvatarPreview;
use \frontend\service\UserService;

/**
 * @var \yii\web\View $this
 * @var \frontend\models\Profile $profileModel
 * @var \frontend\models\forms\UploadFilesForm $uploadFilesModel
 * @var \frontend\models\forms\UserAccountForm $userAccountForm
 * @var \frontend\models\User $user
 * @var \frontend\models\User $userModel
 * @var \frontend\models\UserSettings $userSettingsModel
 */

AccountAvatarPreview::register($this);
?>

<section class="account__redaction-wrapper">
    <h1>Редактирование настроек профиля</h1>
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'post',
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'errorOptions' => ['tag' => 'span', 'class' => 'is-invalid'],
            'checkTemplate' => "{beginLabel}\n{input}<span>{labelTitle}</span>{endLabel}",
            'checkOptions' => [
                'uncheck' => false,
                'tag' => false,
                'class' => 'visually-hidden checkbox__input',
                'labelOptions' => [
                    'class' => 'checkbox__legend'
                ],
            ],
            'options' => [
                'tag' => 'div',
                'class' => 'field-container account__input',
            ]],
        'enableAjaxValidation' => false,
        'enableClientValidation' => true
    ]); ?>
    <div class="account__redaction-section">
        <h3 class="div-line">Настройки аккаунта</h3>
        <div class="account__redaction-section-wrapper">
            <div class="account__redaction-avatar">
                <img src="<?= UserService::getAvatar($user); ?>" width="156" height="156">
                <?= $form->field($uploadFilesModel, 'avatar', [
                    'template' => "{input}\n{label}\n{error}",
                    'options' => ['tag' => false]
                ])->fileInput([
                    'id' => 'upload-avatar',
                    'multiple' => false,
                    'accept' => 'image/*',
                ])->label(null, ['class' => 'link-regular']); ?>
            </div>
            <div class="account__redaction">
                <?= $form->field($userAccountForm, 'name', [
                    'options' => ['class' => 'field-container account__input account__input--name'],
                ])->textInput([
                    'class' => 'input textarea',
                    'value' => Html::encode($user->name),
                ]); ?>

                <?= $form->field($userAccountForm, 'email', [
                    'options' => ['class' => 'field-container account__input account__input--email'],
                ])->textInput([
                    'class' => 'input textarea',
                    'value' => Html::encode($user->email)
                ])->label('email'); ?>

                <?= $form->field($userAccountForm, 'city_id', [
                    'options' => ['class' => 'field-container account__input account__input--address']
                ])->dropdownList(City::getCityMap(), [
                    'class' => 'input textarea',
                    'prompt' => '',
                    'options' => [$user->profile->city_id => ['selected' => true]]
                ]); ?>

                <?= $form->field($userAccountForm, 'birth_date', [
                    'options' => ['class' => 'field-container account__input account__input--date'],
                ])->textInput([
                    'class' => 'input-middle input input-date',
                    'value' => $user->profile->birth_date ? date('d.m.Y', strtotime($user->profile->birth_date)) : null,
                ]); ?>

                <?= $form->field($userAccountForm, 'description', [
                    'options' => ['class' => 'field-container account__input account__input--info'],
                ])->textarea([
                    'class' => 'input textarea',
                    'rows' => 7,
                    'value' => Html::encode($user->profile->description)
                ]); ?>

            </div>
        </div>
        <h3 class="div-line">Выберите свои специализации</h3>
        <div class="account__redaction-section-wrapper">
            <?= $form->field($userAccountForm, 'new_categories_list', [
                'options' => ['tag' => false]
            ])
                ->checkboxList(Category::getCategoryMap(), [
                    'class' => 'search-task__categories account_checkbox--bottom',
                    'item' => function ($index, $label, $name, $checked, $value) use ($user) {
                        return '<label class="checkbox__legend">' .
                            Html::checkbox($name, UserService::checkUserCategory($user, $value), ['value' => $value, 'class' => 'visually-hidden checkbox__input']) .
                            '<span>' . $label . '</span></label>';
                    }
                ])->label(false); ?>
        </div>
        <h3 class="div-line">Безопасность</h3>
        <div class="account__redaction-section-wrapper account__redaction">
            <?= $form->field($userAccountForm, 'password')
                ->passwordInput(['class' => 'input textarea'])
                ->label('Новый пароль'); ?>

            <?= $form->field($userAccountForm, 'password_repeat')
                ->passwordInput(['class' => 'input textarea']); ?>
        </div>

        <h3 class="div-line">Фото работ</h3>
        <div class="portfolio__preview"></div>
        <div class="portfolio__preview-container">
        </div>

        <?= $form->field($uploadFilesModel, 'files[]', [
            'template' => "{label}\n<div class='account__redaction-section-wrapper account__redaction'>{input}\n<span></span></div>\n{error}\n"
        ])->fileInput([
            'id' => 'files',
            'multiple' => true,
            'accept' => 'image/*',
        ])->label('Выбрать фотографии'); ?>


        <h3 class="div-line">Контакты</h3>
        <div class="account__redaction-section-wrapper account__redaction">
            <?= $form->field($userAccountForm, 'phone')->textInput([
                'class' => 'input textarea',
                'value' => $user->profile->phone,
            ]); ?>
            <?= $form->field($userAccountForm, 'skype')->textInput([
                'class' => 'input textarea',
                'value' => $user->profile->skype,
            ]); ?>
            <?= $form->field($userAccountForm, 'telegram')->textInput([
                'class' => 'input textarea',
                'value' => $user->profile->telegram,
            ]); ?>

        </div>
        <h3 class="div-line">Настройки сайта</h3>
        <h4>Уведомления</h4>
        <div class="account__redaction-section-wrapper account_section--bottom">
            <div class="search-task__categories account_checkbox--bottom">
                <?= $form->field($userAccountForm, 'new_message', [
                    'options' => ['tag' => false],
                ])->checkbox([
                    'class' => 'visually-hidden checkbox__input',
                    'checked' => UserService::checkUserSetting($user,'new_message'),

                ]); ?>

                <?= $form->field($userAccountForm, 'task_actions', [
                    'options' => ['tag' => false],
                ])->checkbox([
                    'class' => 'visually-hidden checkbox__input',
                    'checked' => UserService::checkUserSetting($user,'task_actions'),
                ]); ?>

                <?= $form->field($userAccountForm, 'new_recall', [
                    'options' => ['tag' => false],
                ])->checkbox([
                    'class' => 'visually-hidden checkbox__input',
                    'checked' => UserService::checkUserSetting($user,'new_recall'),
                ]); ?>
            </div>

            <div class="search-task__categories account_checkbox account_checkbox--secrecy">
                <?= $form->field($userAccountForm, 'contacts_only_for_client', [
                    'options' => ['tag' => false],
                ])->checkbox([
                    'class' => 'visually-hidden checkbox__input',
                    'checked' => UserService::checkUserSetting($user,'contacts_only_for_client'),
                ]); ?>

                <?= $form->field($userAccountForm, 'hide_profile', [
                    'options' => ['tag' => false],
                ])->checkbox([
                    'class' => 'visually-hidden checkbox__input',
                    'checked' => UserService::checkUserSetting($user,'hide_profile'),
                ]); ?>

            </div>
        </div>
    </div>
    <?= Html::submitButton('Сохранить изменения', ['class' => 'button']); ?>
    <?php ActiveForm::end(); ?>
</section>

