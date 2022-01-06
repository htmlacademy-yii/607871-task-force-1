<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \frontend\models\Respond $respond
 * @var \frontend\models\forms\TaskFinishForm $finishForm
 * @var int $taskId
 */

?>

<section class="modal response-form form-modal" id="response-form">
    <h2>Отклик на задание</h2>
    <?php $form = ActiveForm::begin([
        'id' => 'respond',
        'action' => ['task-respond'],
        'method' => 'post',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => [
                'class' => 'form-modal-description'
            ],
            'errorOptions' => ['tag' => 'span', 'class' => 'registration__text-error'],
            'options' => [
                'tag' => 'p',
            ]],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>

        <?= $form->field($respond, 'rate')->textInput(['class' => 'response-form-payment input input-middle input-money']); ?>

        <?= $form->field($respond, 'description')->textarea([
            'class' => 'input textarea',
            'rows' => 4,
            'placeholder' => 'Place your text',
        ]); ?>

    <?= $form->field($respond, 'task_id')->hiddenInput(['value' => $taskId])->label(false); ?>
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
    <a href="<?=Url::to(["/task/refuse/{$taskId}"]); ?>" class="button__form-modal refusal-button button"
            type="button">Отказаться
    </a>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>


<section class="modal form-modal refusal-form" id="cancel-form">
    <h2>Отмена задания</h2>
    <p>
        Вы собираетесь отменить задание.
        Вы уверены?
    </p>
    <button class="button__form-modal button" id="close-modal2"
            type="button">Нет
    </button>
    <a href="<?=Url::to(["/task/cancel/{$taskId}"]); ?>" class="button__form-modal refusal-button button"
       type="button">Да
    </a>
    <button class="form-modal-close" type="button">Закрыть</button>
</section>
