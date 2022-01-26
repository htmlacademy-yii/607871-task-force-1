<?php

namespace frontend\assets;

use yii\web\AssetBundle;
use yii\web\View;

class YandexMapApiAsset extends AssetBundle
{
    public $jsOptions = ['position' => View::POS_HEAD];

    public function init()
    {
        parent::init();
        $this->js = [
            'https://api-maps.yandex.ru/2.1/?apikey=' . \Yii::$app->params['yandexAPIKey'] . '&lang=ru_RU',
        ];
    }
}
