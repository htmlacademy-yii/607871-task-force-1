<?php

use \yii\widgets\ActiveForm;
use \frontend\models\Category;
use \yii\helpers\Html;
use frontend\assets\AutoCompleteAsset;

/**
 * @var \yii\web\View $this
 * @var \frontend\models\forms\UploadFilesForm $uploadFiles
 * @var \frontend\models\forms\CreateTaskForm $createTaskForm
 */

AutoCompleteAsset::register($this);

?>
<section class="create__task">
    <h1>Публикация нового задания</h1>
    <div class="create__task-main" id="form-container">
        <?php $form = ActiveForm::begin([
            'id' => 'task-form',
            'action' => ['create'],
            'method' => 'post',
            'options' => [
                'class' => 'create__task-form form-create',
                'enctype' => 'multipart/form-data',
            ],
            'fieldConfig' => [
                'template' => "{label}\n{input}\n{hint}\n{error}",
                'hintOptions' => ['tag' => 'span'],
                'errorOptions' => ['tag' => 'span', 'class' => 'registration__text-error'],
                'options' => [
                    'tag' => 'div',
                    'class' => 'field-container',
                ],
            ],
            'enableAjaxValidation' => false,
            'enableClientValidation' => true,
            'validateOnSubmit' => true
        ]); ?>

        <?= $form->field($createTaskForm, 'title')->textInput([
            'class' => 'input textarea',
            'placeholder' => 'Повесить полку',
        ])->hint('Кратко опишите суть работы'); ?>

        <?= $form->field($createTaskForm, 'description')->textarea([
            'class' => 'input textarea',
            'placeholder' => 'Place your text',
            'rows' => 7,
        ])->hint('Укажите все пожелания и детали, чтобы исполнителям было проще соориентироваться'); ?>

        <?= $form->field($createTaskForm, 'category_id')->dropDownList(Category::getCategoryMap(), [
            'prompt' => '',
            'class' => 'multiple-select input multiple-select-big',
            'size' => 1
        ])->hint('Выберите категорию'); ?>

        <?= $form->field($uploadFiles, 'files[]', [
            'template' => "{label}\n<span>{hint}</span>\n<div class='create__file dz-clickable '>{input}\n<span>Добавить новый файл</span></div>\n{error}\n"
        ])->fileInput([
            'id' => 'files',
            'multiple' => true,
            'accept' => 'image/*, .pdf, .docx, .doc, .txt, .xls, .csv',
        ])
            ->hint('Загрузите файлы, которые помогут исполнителю лучше выполнить или оценить работу'); ?>

        <?= $form->field($createTaskForm, 'full_address')
            ->input('search', [
                'class' => 'input-navigation input-middle input',
                'placeholder' => 'Санкт-Петербург, Калининский район',
                'id' => 'autoComplete'
            ])->hint('Укажите адрес исполнения, если задание требует присутствия'); ?>

        <?= $form->field($createTaskForm, 'latitude')->hiddenInput()->label(false); ?>
        <?= $form->field($createTaskForm, 'longitude')->hiddenInput()->label(false); ?>
        <?= $form->field($createTaskForm, 'city_name')->hiddenInput()->label(false); ?>
        <?= $form->field($createTaskForm, 'address')->hiddenInput()->label(false); ?>

        <div class="create__price-time">
                <?= $form->field($createTaskForm, 'budget',['options'=>['class' => 'field-container create__price-time--wrapper']])->textInput([
                    'class' => 'input textarea input-money',
                    'placeholder' => 1000,
                ])->hint('Не заполняйте для оценки исполнителем'); ?>

                <?= $form->field($createTaskForm, 'due_date', ['options'=>['class' => 'field-container create__price-time--wrapper']])->textInput([
                    'class' => 'input-middle input input-date',
                    'placeholder' => 'ГГГГ-ММ-ДД',
                ])->hint('Укажите крайний срок исполнения'); ?>
        </div>

        <?php ActiveForm::end(); ?>

        <div class="create__warnings">
            <div class="warning-item warning-item--advice">
                <h2>Правила хорошего описания</h2>
                <h3>Подробности</h3>
                <p>Друзья, не используйте случайный<br>
                    контент – ни наш, ни чей-либо еще. Заполняйте свои
                    макеты, вайрфреймы, мокапы и прототипы реальным
                    содержимым.</p>
                <h3>Файлы</h3>
                <p>Если загружаете фотографии объекта, то убедитесь,
                    что всё в фокусе, а фото показывает объект со всех
                    ракурсов.</p>
            </div>

            <?php if ($createTaskForm->errors || $uploadFiles->errors): ?>
                <div class="warning-item warning-item--error">
                    <h2>Ошибки заполнения формы</h2>
                    <?php foreach ($createTaskForm->errors as $label => $errors): ?>
                        <h3> <?= $createTaskForm->getAttributeLabel($label); ?></h3>
                        <p><?php foreach ($errors as $error): ?>
                                <?= $error; ?><br>
                            <?php endforeach; ?>
                        </p>
                    <?php endforeach; ?>
                    <?php foreach ($uploadFiles->errors as $label => $errors): ?>
                        <h3> <?= $uploadFiles->getAttributeLabel($label); ?></h3>
                        <p><?php foreach ($errors as $error): ?>
                                <?= $error; ?><br>
                            <?php endforeach; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?= Html::submitButton('Опубликовать', ['class' => "button", 'form' => 'task-form', 'id' => 'form-submit-button']); ?>
</section>
