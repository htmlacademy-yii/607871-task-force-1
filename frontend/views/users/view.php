<?php

use App\Service\DataFormatter;
use yii\helpers\Url;
use \frontend\widgets\UserRatingWidget;

/**
 * @var \frontend\models\User $user
 * @var \frontend\models\forms\TaskSearchForm $model
 */

?>
<section class="content-view">
    <div class="user__card-wrapper">
        <div class="user__card">
            <img src="<?= $user->avatar; ?>" width="120" height="120" alt="Аватар пользователя">
            <div class="content-view__headline">
                <h1><?= $user->name; ?></h1>
                <p><?= $user->profile->city->name; ?>
                    <?= $user->profile->birth_date ? ', ' . DataFormatter::formatTimeDistance($user->profile->birth_date) : ''; ?>
                </p>
                <div class="profile-mini__name five-stars__rate">
                    <?= UserRatingWidget::widget(['userRating' => $user->rating]); ?>
                </div>
                <b class="done-task">Выполнил
                    <?= DataFormatter::declensionOfNouns($user->executorTasksFinished, ['заказ', 'заказа', 'заказов']); ?></b>
                <b class="done-review">Получил
                    <?= DataFormatter::declensionOfNouns(count($user->recalls), ['отзыв', 'отзыва', 'отзывов']); ?></b>
            </div>
            <div class="content-view__headline user__card-bookmark
            <?= Yii::$app->user->identity->checkIsUserFavorite($user->id) ? 'user__card-bookmark--current' : 'user__card-bookmark'; ?>">
                <span>Был на сайте <?= DataFormatter::getRelativeTime($user->last_visit_date); ?></span>
                <a href="<?= Url::to(["/favorite/{$user->id}"]) ?>"><b></b></a>
            </div>
        </div>
        <div class="content-view__description">
            <p><?= $user->profile->description; ?></p>
        </div>
        <div class="user__card-general-information">
            <div class="user__card-info">
                <h3 class="content-view__h3">Специализации</h3>
                <div class="link-specialization">
                    <?php foreach ($user->categories as $category): ?>
                        <a href="<?= Url::to([
                            '/tasks/index', "{$model->formName()}" =>
                                ['categories' => [$category->id],
                                    'noExecutor' => false
                                ]
                        ]); ?>" class="link-regular"><?= $category->name; ?></a>
                    <? endforeach; ?>
                </div>
                <h3 class="content-view__h3">Контакты</h3>
                <div class="user__card-link">
                    <a class="user__card-link--tel link-regular" href="#"><?= $user->profile->phone; ?></a>
                    <a class="user__card-link--email link-regular" href="#"><?= $user->email; ?></a>
                    <a class="user__card-link--skype link-regular" href="#"><?= $user->profile->skype; ?></a>
                </div>
            </div>
            <div class="user__card-photo">
                <h3 class="content-view__h3">Фото работ</h3>
                <?php foreach ($user->portfolios as $portfolio): ?>
                    <a href="<?= $portfolio['file']; ?>"><img src="<?= $portfolio['file']; ?>" width="85" height="86"
                                                              alt="Фото работы"></a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="content-view__feedback">
        <?php if (count($user->recalls) > 0): ?>
        <h2>Отзывы<span>(<?= count($user->recalls); ?>)</span></h2>
        <div class="content-view__feedback-wrapper reviews-wrapper">
            <?php foreach ($user->recalls as $recall): ?>
                <div class="feedback-card__reviews">
                    <p class="link-task link">Задание <a href="<?= Url::to("/task/view/{$recall->task->id}") ?>"
                                                         class="link-regular">«<?= $recall->task->title; ?>»</a></p>
                    <div class="card__review">
                        <a href="<?= Url::to("/user/view/{$recall->task->client->id}"); ?>"><img
                                    src="<?= $recall->task->client->avatar; ?>" width="55" height="54"></a>
                        <div class="feedback-card__reviews-content">
                            <p class="link-name link"><a
                                        href="<?= Url::to("/user/view/{$recall->task->client->id}"); ?>"
                                        class="link-regular"><?= $recall->task->client->name; ?></a></p>
                            <p class="review-text">
                                <?= $recall->description; ?>
                            </p>
                        </div>
                        <div class="card__review-rate">
                            <p class="<?= $recall->rating > 3 ? 'five-rate' : 'three-rate' ?> big-rate"><?= $recall->rating; ?>
                                <span></span></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
<section class="connect-desk">
    <div class="connect-desk__chat">

    </div>
</section>
