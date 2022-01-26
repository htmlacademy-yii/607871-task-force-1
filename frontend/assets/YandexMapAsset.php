<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class YandexMapAsset extends AssetBundle
{
    public $js = [
        'js/task-map.js',
    ];

    public $depends = [
        JqueryAsset::class,
        YandexMapApiAsset::class,
    ];
}
