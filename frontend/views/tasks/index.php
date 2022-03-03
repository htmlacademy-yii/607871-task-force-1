<?php
use yii\helpers\Url;
use App\Service\DataFormatter;
use yii\widgets\LinkPager;
use \yii\helpers\Html;
use \frontend\assets\TaskIndexAsset;

/**
 * @var \yii\web\View $this
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \frontend\models\forms\TaskSearchForm $model
 */

TaskIndexAsset::register($this);
?>
<section class="new-task">
    <div class="new-task__wrapper">
        <h1>Новые задания</h1>
        <?php foreach ($dataProvider->getModels() as $newTask): ?>
            <div class="new-task__card">
                <div class="new-task__title">
                    <a href="<?= Url::to(
                    "/task/view/{$newTask->id}"); ?>" class="link-regular"><h2><?= Html::encode($newTask->title); ?></h2></a>
                    <a class="new-task__type link-regular" href="<?= Url::to([
                        '/tasks/index', "{$model->formName()}"=>
                            ['categories' => [$newTask->category->id],
                                'noExecutor' => false
                            ]
                    ]); ?>"><p><?= $newTask->category->name; ?></p></a>
                </div>
                <div class="new-task__icon new-task__icon--<?= $newTask->category->icon; ?>"></div>
                <p class="new-task_description">
                    <?= Html::encode($newTask->description); ?>
                </p>
                <b class="new-task__price new-task__price--<?= $newTask->category->icon; ?>">
                    <?= $newTask->budget ? "{$newTask->budget}&nbsp;₽" : ''; ?></b>
                <p class="new-task__place"><?= $newTask->city->name ?? ''; ?><?= $newTask->district ? ", {$newTask->district}" : ''; ?></p>
                <span class="new-task__time"><?= DataFormatter::getRelativeTime($newTask->creation_date); ?></span>
            </div>
        <?php endforeach; ?>
    </div>
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

<?= Yii::$app->controller->renderPartial('/tasks/search-task', ['model' => $model]); ?>



