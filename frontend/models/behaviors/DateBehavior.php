<?php

namespace frontend\models\behaviors;

use yii\base\Behavior;

class DateBehavior extends Behavior
{
    public function getRelativeTime($value)
    {
        return $value ? \Yii::$app->formatter->asRelativeTime($value) : null;
    }
}