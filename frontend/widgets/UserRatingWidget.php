<?php


namespace frontend\widgets;


use yii\base\Widget;

class UserRatingWidget extends Widget
{
    public $userRating;

    public function run()
    {
        return $this->render('user-rating-widget', [
            'userRating'=> $this->userRating,
        ]);
    }
}