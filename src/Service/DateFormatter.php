<?php

namespace App\Service;

class DateFormatter
{
    public static function getRelativeTime($value)
    {
        return $value ? \Yii::$app->formatter->asRelativeTime($value) : null;
    }

    public static function getDuration($value)
    {
        return str_replace(' назад', '', self::getRelativeTime($value));
    }
}