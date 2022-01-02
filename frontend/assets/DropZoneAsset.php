<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class DropZoneAsset extends AssetBundle
{
    public $js = [
        '/js/dropzone.js',
        '/js/myDropzone.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
