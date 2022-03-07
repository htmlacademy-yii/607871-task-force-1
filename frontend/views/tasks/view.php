<?php

use App\Service\DataFormatter;
use frontend\assets\YandexMapAsset;
use \yii\helpers\Url;
use \frontend\models\Task;
use \frontend\models\Respond;
use frontend\assets\TaskViewAsset;
use yii\helpers\Html;
use \frontend\widgets\UserRatingWidget;

/**
 * @var \yii\web\View $this
 * @var \frontend\models\Task $task
 * @var \frontend\models\User $userCard
 * @var \frontend\models\forms\TaskSearchForm $model
 * @var array $actions
 */

TaskViewAsset::register($this);
YandexMapAsset::register($this);

?>
<section class="content-view">
    <div class="content-view__card">
        <div class="content-view__card-wrapper">
            <div class="content-view__header">
                <div class="content-view__headline">
                    <h1><?= Html::encode($task->title); ?></h1>
                    <span>Размещено в категории
                                    <a href="<?= Url::to([
                                        '/tasks/index', "{$model->formName()}" =>
                                            ['categories' => [$task->category->id],
                                                'noExecutor' => false
                                            ]
                                    ]); ?>" class="link-regular"><?= $task->category->name; ?></a>
                                    <?= DataFormatter::getRelativeTime($task->creation_date); ?></span>
                </div>
                <b class="new-task__price new-task__price--clean content-view-price">
                        <?= $task->budget ? "{$task->budget}&nbsp;₽" : ''; ?>
                    </b></b>
                <div class="new-task__icon new-task__icon--<?= $task->category->icon; ?> content-view-icon"></div>
            </div>
            <div class="content-view__description">
                <h3 class="content-view__h3">Общее описание</h3>
                <p>
                    <?= Html::encode($task->description); ?>
                </p>
            </div>
            <div class="content-view__attach">
                <h3 class="content-view__h3">Вложения</h3>
                <?php foreach ($task->taskFiles as $file): ?>
                    <a href="<?= Url::to($file['url']); ?>"><?= $file['name']; ?></a>
                <?php endforeach; ?>
            </div>
            <div class="content-view__location">
                <h3 class="content-view__h3">Расположение</h3>
                <div class="content-view__location-wrapper">
                    <div class="content-view__map">
                        <a href="#">
                            <div id="map" style="width: 361px; height: 292px" data-lat="<?= $task->latitude ?? ''; ?>"
                                 data-lon="<?= $task->longitude ?? ''; ?>">
                            </div>
                        </a>
                    </div>
                    <div class="content-view__address">
                        <span class="address__town"><?= $task->city->name ?? 'Город не определен'; ?></span><br>
                        <span><?= $task->address ?? 'Удаленная работа'; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-view__action-buttons">
            <?php foreach ($actions as $action): ?>
                <button class="button button__big-color <?= $action->getButtonColorClass(); ?>-button open-modal"
                        type="button" data-for="<?= $action->getActionCode(); ?>-form"><?= $action->getActionTitle(); ?>
                </button>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if ($task->getResponds()->count()): ?>
        <?php if (Yii::$app->user->id === $task->client->id || $task->isVolunteer(Yii::$app->user->id)): ?>
            <div class="content-view__feedback">
                <h2>Отклики <span>(<?= count($task->responds); ?>)</span></h2>
                <div class="content-view__feedback-wrapper">

                    <?php foreach ($task->responds as $respond): ?>
                        <?php if (Yii::$app->user->id === $task->client->id || Yii::$app->user->id === $respond->volunteer->id): ?>
                            <div class="content-view__feedback-card">
                                <div class="feedback-card__top">
                                    <a href="<?= Url::to("/user/view/{$respond->volunteer->id}"); ?>"><img
                                                src="<?= $respond->volunteer->avatar; ?>" width="55" height="55"></a>
                                    <div class="feedback-card__top--name">
                                        <p><a href="<?= Url::to("/user/view/{$respond->volunteer->id}"); ?>"
                                              class="link-regular"><?= Html::encode($respond->volunteer->name); ?></a></p>
                                        <?= UserRatingWidget::widget(['userRating' => $respond->volunteer->rating]); ?>
                                    </div>
                                    <span class="new-task__time"><?= DataFormatter::getRelativeTime($respond->creation_date); ?></span>
                                </div>
                                <div class="feedback-card__content">
                                    <p>
                                        <?= Html::encode($respond->description); ?>
                                    </p>
                                    <span><?= $respond->rate; ?>&nbsp;₽</span>
                                </div>
                                <?php if (Yii::$app->user->id === $task->client->id && $task->status == Task::STATUS_NEW && $respond->status == Respond::STATUS_NEW): ?>
                                    <div class="feedback-card__actions">
                                        <a href="<?= Url::to(["/task/confirm/{$task->id}/{$respond->id}"]); ?>"
                                           class="button__small-color response-button button"
                                           type="button">Подтвердить</a>
                                        <a href="<?= Url::to(["/task/deny/{$task->id}/{$respond->id}"]); ?>"
                                           class="button__small-color refusal-button button"
                                           type="button">Отказать</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<section class="connect-desk">
    <div class="connect-desk__profile-mini">
        <div class="profile-mini__wrapper">
            <h3><?= ($userCard->id === $task->client_id) ?  'Заказчик' : 'Исполнитель'?></h3>
            <div class="profile-mini__top">
                <img src="<?= Html::encode($userCard->avatar); ?>" width="62" height="62" alt="Аватар заказчика">
                <div class="profile-mini__name five-stars__rate">
                    <p><?= Html::encode($userCard->name); ?></p>
                </div>
            </div>
            <p class="info-customer">
                <span><?= DataFormatter::declensionOfNouns(count($userCard->clientTasks), ['задание', 'задания', 'заданий']); ?></span><span
                        class="last-"><?= DataFormatter::formatTimeDistance($userCard->reg_date); ?> на сайте</span></p>
            <a href="#" class="link-regular">Смотреть профиль</a>
        </div>
    </div>
    <?php if ($task->status === Task::STATUS_IN_PROGRESS  && (Yii::$app->user->id === $task->client_id || Yii::$app->user->id === $task->executor_id)): ?>
        <div id="chat-container">
            <chat class="connect-desk__chat" task="<?= $task->id; ?>">
            </chat>
        </div>
    <?php endif; ?>
</section>
