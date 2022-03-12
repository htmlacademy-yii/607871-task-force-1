<?php


namespace frontend\widgets;


use yii\base\Widget;

class CityWidget extends Widget
{
    /**
     * Виджет отображает список городов из справочника базы данных.
     * Причем, городу, установленный у пользователя в сессии будет выбран в этом списке по умолчанию.
     * @return string
     */
    public function run()
    {
        return $this->render('city-widget', ['city_id' => \Yii::$app->session->get('city_id')]);
    }

}