<?php
use yii\helpers\Url;
use App\Service\DataFormatter;
?>
<section class="user__search">
    <?php foreach ($dataProvider->getModels() as $user): ?>
        <div class="content-view__feedback-card user__search-wrapper">
            <div class="feedback-card__top">
                <div class="user__search-icon">
                    <a href="/user.html"><img src="<?= $user->avatar; ?>" width="65" height="65"></a>
                    <span><?= DataFormatter::declensionOfNouns($user->executorTasksFinished, ['задание', 'задания', 'заданий']); ?></span>
                    <span><?= DataFormatter::declensionOfNouns(count($user->recalls), ['отзыв', 'отзыва', 'отзывов']); ?></span>
                </div>
                <div class="feedback-card__top--name user__search-card">
                    <p class="link-name"><a href="<?= Url::to(
                            "user/view/{$user->id}"); ?>" class="link-regular"><?= $user->name; ?></a></p>
                    <span></span><span></span><span></span><span></span><span class="star-disabled"></span>
                    <b><?= $user->rating; ?></b>
                    <p class="user__search-content">
                        <?= $user->profile->description; ?>
                    </p>
                </div>
                <span class="new-task__time">Был на сайте <?= DataFormatter::getRelativeTime($user->last_visit_date); ?></span>
            </div>
            <div class="link-specialization user__search-link--bottom">

                <?php foreach ($user->categories as $category): ?>
                    <a href="<?= \yii\helpers\Url::to([
                        'users/index', "{$model->formName()}"=>
                            ['categories' => [$category->id]]
                    ]); ?>" class="link-regular">
                        <?= $category->name; ?>
                    </a>

                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?= Yii::$app->controller->renderPartial('/users/search-user', ['model' => $model]); ?>
