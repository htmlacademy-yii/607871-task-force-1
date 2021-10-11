<?php

use yii\bootstrap4\ActiveForm;
use \yii\helpers\Html;

?>

<section class="search-task">
    <div class="search-task__wrapper">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => ['class' => 'search-task__form', 'data' => ['pjax' => true]],
            'fieldConfig' => [
                'checkTemplate' => "{beginLabel}\n{input}<span>{labelTitle}</span>{endLabel}",
                'checkOptions' => [
                    'class' => 'visually-hidden checkbox__input',
                    'labelOptions' => [
                        'class' => 'checkbox__legend'
                    ],
                ],
                'options' => [
                    'tag' => false,
                ]],
            'enableAjaxValidation' => false,
        ]); ?>
        <fieldset class="search-task__categories">
            <legend>Категории</legend>
            <?= $form->field($model, 'categories', [
                'template' => "{input}",
                'options' => ['tag' => false]
            ])
                ->checkboxList(\frontend\models\Category::getCategoryMap(),
                    [
                        'item' => function ($index, $label, $name, $checked, $value) {
                            return '<label class="checkbox__legend">' . Html::checkbox($name, $checked, ['value' => $value, 'class' => 'visually-hidden checkbox__input']) .
                                '<span>' . $label . '</span></label>';
                        }
                    ]); ?>
        </fieldset>
        <fieldset class="search-task__categories">
            <legend>Дополнительно</legend>
            <?= $form->field($model, 'noExecutor')->checkbox(); ?>
            <?= $form->field($model, 'remoteWork')->checkbox(); ?>
        </fieldset>
        <?= $form->field($model, 'period', [
            'labelOptions' => ['class' => 'search-task__name']
        ])->dropDownList($model::PERIOD_MAP, [
            'class' => 'multiple-select input',

        ])?>
        <?= $form->field($model, 'titleSearch', [
            'labelOptions' => ['class' => 'search-task__name']
        ])->textInput(['class' => "input-middle input"]) ?>
        <div class="form-group">
            <?= Html::submitButton('Искать', ['class' => "button", 'type' => 'submit']) ?>
        </div>
        <? ActiveForm::end(); ?>
    </div>
</section>
