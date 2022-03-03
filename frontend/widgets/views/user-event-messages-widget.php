<?php

use frontend\models\UserMessage;
use \yii\helpers\Url;

/**
* @var array $userMessages
 */

?>

<div class="header__lightbulb"></div>
<div class="lightbulb__pop-up">
    <h3>Новые события</h3>
    <?php foreach ($userMessages as $userMessage): ?>
        <p class="lightbulb__new-task <?= UserMessage::CSS_ICON_CLASS_MAP[$userMessage->type]; ?>">
            <?= UserMessage::TYPE_MESSAGE_MAP[$userMessage->type]; ?>
            <a href="<?= Url::to("/task/view/{$userMessage->task->id}")?>" class="link-regular">«<?= $userMessage->task->title; ?>»</a>
        </p>
    <?php endforeach; ?>
</div>

