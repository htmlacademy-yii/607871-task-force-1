<?php

use \yii\widgets\ActiveForm;
use \frontend\models\Category;
use \yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \frontend\models\Task $task
 * @var \frontend\models\forms\UploadFilesForm $uploadFiles
 */



\frontend\assets\DropZoneAsset::register($this);
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
            'enableClientValidation' => false,
            'validateOnSubmit' => true

        ]); ?>

        <div class="field-container">
            <?= $form->field($task, 'title')->textInput([
                'class' => 'input textarea',
                'placeholder' => 'Повесить полку',
            ])->hint('Кратко опишите суть работы'); ?>

        </div>
        <div class="field-container">
            <?= $form->field($task, 'description')->textarea([
                'class' => 'input textarea',
                'placeholder' => 'Place your text',
                'rows' => 7,
            ])->hint('Укажите все пожелания и детали, чтобы исполнителям было проще соориентироваться'); ?>
        </div>
        <div class="field-container">
            <?= $form->field($task, 'category_id')->dropDownList(Category::getCategoryMap(), [
                'prompt' => '',
                'class' => 'multiple-select input multiple-select-big',
                'size' => 1
            ])->hint('Выберите категорию'); ?>
        </div>
        <div class="field-container">
            <?= $form->field($uploadFiles, 'files[]', ['options' => ['tag' => false]
                , 'template' => "{label}\n<span>{hint}</span>\n<div class='create__file dz-clickable '>{input}\n<span>Добавить новый файл</span>\n{error}\n</div>
<div id='preview-template'></div>"
            ])->fileInput([
                'id' => 'files',
                'multiple' => true,
                'accept' => 'image/*, .pdf, .docx, .doc, .txt, .xls, .csv',
            ])
                ->hint('Загрузите файлы, которые помогут исполнителю лучше выполнить или оценить работу'); ?>
        </div>


        <div class="field-container">
            <?= $form->field($task, 'address')
                ->input('search', [
                    'class' => 'input-navigation input-middle input',
                    'placeholder' => 'Санкт-Петербург, Калининский район',
                ])->hint('Укажите адрес исполнения, если задание требует присутствия'); ?>

            <?= $form->field($task, 'latitude')->hiddenInput()->label(false); ?>
            <?= $form->field($task, 'longitude')->hiddenInput()->label(false); ?>
        </div>
        <div class="create__price-time">
            <div class="field-container create__price-time--wrapper">
                <?= $form->field($task, 'budget')->textInput([
                    'class' => 'input textarea input-money',
                    'placeholder' => 1000,
                ])->hint('Не заполняйте для оценки исполнителем'); ?>
            </div>
            <div class="field-container create__price-time--wrapper">
                <?= $form->field($task, 'due_date')->textInput([
                    'class' => 'input-middle input input-date',
                    'placeholder' => 'ГГГГ-ММ-ДД',
                ])->hint('Укажите крайний срок исполнения'); ?>
            </div>
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

            <?php if ($task->errors): ?>
                <div class="warning-item warning-item--error">
                    <h2>Ошибки заполнения формы</h2>
                    <?php foreach ($task->errors as $label => $errors): ?>
                        <h3> <?= $task->getAttributeLabel($label) ?></h3>

                        <p><?php foreach ($errors as $error): ?>
                                <?= $error ?><br>
                            <?php endforeach; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?= Html::submitButton('Опубликовать', ['class' => "button", 'form' => 'task-form', 'id' => 'form-submit-button']); ?>
</section>
