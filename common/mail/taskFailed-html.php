<?php
/**
 * @var \frontend\models\User $user
 * @var \frontend\models\Task $task
 */
?>

<h1>Получено новое уведомление oт портала TaskForce</h1>
<p>Здравствуйте, <?= $user->name; ?></p>
<p>Вы получили данное уведомление по заданию "<?= $task->title ;?>" потому что назначенный вами
    исполнитель отказался от его выполнения.</p>
<p>Для получения более детальной информации перейдите по ссылке
    <a href="<?= Yii::$app->urlManager->createAbsoluteUrl("task/view/{$task->id}"); ?>">
        <?= Yii::$app->urlManager->createAbsoluteUrl("task/view/{$task->id}"); ?>
    </a>

</p>
