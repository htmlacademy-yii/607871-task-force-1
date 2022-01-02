<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var \frontend\models\Respond $respond
 * @var \frontend\models\forms\TaskFinishForm $finishForm
 */

?>

<section class="modal response-form form-modal" id="response-form">
    <h2>Отклик на задание</h2>
    <?php $form = ActiveForm::begin([
        'id' => 'respond',
        'action' => ['task-respond'],
        'method' => 'post',
        'fieldConfig' => [
            'template' => "{label}\n{input}",
            'labelOptions' => [
                'class' => 'form-modal-description'
            ],
            'options' => [
                'tag' => false,
            ]],
        'enableAjaxValidation' => false,
    ]); ?>
    <p>
        <?= $form->field($respond, 'rate')->textInput(['class' => 'response-form-payment input input-middle input-money']); ?>
    </p>
    <p>
        <?= $form->field($respond, 'description')->textarea([
            'class' => 'input textarea',
            'rows' => 4,
            'placeholder' => 'Place your text',
        ]); ?>
    </p>
    <?= Html::submitButton('Отправить', ['class' => "button modal-button"]); ?>
    <?php ActiveForm::end(); ?>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>


<section class="modal completion-form form-modal" id="complete-form">
    <h2>Завершение задания</h2>
    <p class="form-modal-description">Задание выполнено?</p>
    <?php $form = ActiveForm::begin([
        'id' => 'finish',
        'action' => ['task-finish'],
        'method' => 'post',
        'fieldConfig' => [
            'template' => "{label}\n{input}",
            'options' => [
                'tag' => false,
            ]],
        'enableAjaxValidation' => false,
    ]); ?>
    <?=$form->field($finishForm, 'status', [
        'template' => "{input}",
        'options' => ['tag' => false]
    ])
        ->radioList($finishForm::COMPLETION,
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    $radio = Html::radio(
                        $name,
                        $checked,
                        [
                            'id' => $value,
                            'value' => $value,
                            'class' => 'visually-hidden completion-input completion-input--' . $value
                        ]
                    );
                    $label = Html::label(
                        $label,
                        $value,
                        [
                            'class' => 'completion-label completion-label--' . $value
                        ]
                    );

                    return $radio . $label;
                },
                'unselect' => null
            ]); ?>

    <p>
        <?= $form->field($finishForm, 'description', [
            'labelOptions' => ['class' => 'form-modal-description']
        ])->textarea([
            'class' => 'input textarea',
            'rows' => 4,
            'placeholder' => 'Place your text',
        ]); ?>
    </p>

    <?= $form->field(
        $finishForm,
        'rating',
        [
            'template' => "<p class='form-modal-description'>{label}
                    <div class='feedback-card__top--name completion-form-star'>
                        <span class='star-disabled'></span>
                        <span class='star-disabled'></span>
                        <span class='star-disabled'></span>
                        <span class='star-disabled'></span>
                        <span class='star-disabled'></span>
                    </div>
                    {input}</p>{error}"
        ]
    )->hiddenInput(['id' => 'rating']); ?>
    <input type="hidden" name="rating" id="rating">
    <?= Html::submitButton('Отправить', ['class' => "button modal-button"]); ?>
    <?php ActiveForm::end(); ?>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>
<section class="modal form-modal refusal-form" id="refuse-form">
    <h2>Отказ от задания</h2>
    <p>
        Вы собираетесь отказаться от выполнения задания.
        Это действие приведёт к снижению вашего рейтинга.
        Вы уверены?
    </p>
    <button class="button__form-modal button" id="close-modal"
            type="button">Отмена
    </button>
    <button class="button__form-modal refusal-button button"
            type="button">Отказаться
    </button>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>
