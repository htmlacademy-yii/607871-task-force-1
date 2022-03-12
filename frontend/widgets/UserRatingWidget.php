<?php


namespace frontend\widgets;


use yii\base\Widget;

class UserRatingWidget extends Widget
{
    public $userRating;

    /**
     * Виджет отображает рейтинг пользователя в виде набора звездочек.
     * @return string
     */
    public function run()
    {
        return $this->render('user-rating-widget', ['userRating'=> $this->userRating]);
    }
}