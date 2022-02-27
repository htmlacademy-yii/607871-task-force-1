<?php


namespace frontend\widgets;


use yii\base\Widget;

class CityWidget extends Widget
{
    public function run()
    {
        return $this->render('city-widget', ['city_id' => \Yii::$app->session->get('city_id')]);
    }

}