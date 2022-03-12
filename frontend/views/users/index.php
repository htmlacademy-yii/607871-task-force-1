<?php
use yii\helpers\Url;
use App\Service\DataFormatter;
use \frontend\widgets\UserRatingWidget;
use \yii\helpers\Html;
use yii\widgets\LinkPager;
use \frontend\assets\TaskIndexAsset;
use \frontend\service\UserService;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \frontend\models\forms\UserSearchForm $model
 */

TaskIndexAsset::register($this);
?>
<section class="user__search">
    <?php foreach ($dataProvider->getModels() as $user): ?>
        <div class="content-view__feedback-card user__search-wrapper">
            <div class="feedback-card__top">
                <div class="user__search-icon">
                    <a href="<?= Url::to(
                        "/user/view/{$user->id}"); ?>"><img src="<?= UserService::getAvatar($user); ?>" width="65" height="65"></a>
                    <span><?= DataFormatter::declensionOfNouns($user->executorTasksFinished, ['задание', 'задания', 'заданий']); ?></span>
                    <span><?= DataFormatter::declensionOfNouns(count($user->recalls), ['отзыв', 'отзыва', 'отзывов']); ?></span>
                </div>
                <div class="feedback-card__top--name user__search-card">
                    <p class="link-name"><a href="<?= Url::to(
                            "/user/view/{$user->id}"); ?>" class="link-regular"><?= Html::encode($user->name); ?></a></p>
                    <?= UserRatingWidget::widget(['userRating' => $user->rating]); ?>
                    <p class="user__search-content">
                        <?= Html::encode($user->profile->description); ?>
                    </p>
                </div>
                <span class="new-task__time">Был на сайте <?= DataFormatter::getRelativeTime($user->last_visit_date); ?></span>
            </div>
            <div class="link-specialization user__search-link--bottom">

                <?php foreach ($user->categories as $category): ?>
                    <a href="<?= Url::to([
                        '/users/index', "{$model->formName()}"=>
                            ['categories' => [$category->id]]
                    ]); ?>" class="link-regular">
                        <?= $category->name; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="new-task__pagination">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->getPagination(),
            'options' => ['class' => 'new-task__pagination-list'],
            'activePageCssClass' => 'pagination__item--current',
            'pageCssClass' => 'pagination__item',
            'nextPageCssClass' => 'pagination__item',
            'prevPageCssClass' => 'pagination__item',
            'nextPageLabel' => '',
            'prevPageLabel' => '',
        ]); ?>
    </div>
</section>
<?= Yii::$app->controller->renderPartial('/users/search-user', ['model' => $model]); ?>
