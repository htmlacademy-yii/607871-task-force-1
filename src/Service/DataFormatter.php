<?php

namespace App\Service;

class DataFormatter
{
    public static function getRelativeTime($value)
    {
        return $value ? \Yii::$app->formatter->asRelativeTime($value) : null;
    }

    /**
     * Числовое склонение единиц измерения.
     * @param int $number - число, для еденицы измерения которого нужно выполнить склонение.
     * @param array $nouns - массив с вариантами склонения единицы измерения: [для числа 1, для чисел 2-4, для остальных чисел].
     *
     * @return string - возвращается число и соответствующее склонение единицы измерения.
     */
    public static function declensionOfNouns(int $number, array $nouns): string
    {
        $noun = $nouns[2] ?? ''; // Значение по умолчанию.

        $division_remainder = $number % 10;
        if ($division_remainder === 1) {
            $noun = $nouns[0] ?? '';
        }
        if (in_array($division_remainder, [2, 3, 4])) {
            $noun = $nouns[1] ?? '';
        }

        if ($number >= 11 && $number <= 14) {
            $noun = $nouns[2] ?? '';
        }

        return "$number $noun";
    }

    public static function formatTimeDistance(string $date): string
    {

        $reg_date = strtotime($date);
        $now = strtotime(date('Y-m-d H:i:s'));
        $time_difference = $now - $reg_date;

        $time_declensions = [

            'days' => ['день', 'дня', 'дней'],
            'hours' => ['час', 'часа', 'часов'],
            'minutes' => ['минуту', 'минуты', 'минут']
        ];

        $time_distance = [
            'hours' => floor($time_difference / 3600),
            'minutes' => floor($time_difference / 60)
        ];

        if($time_distance['hours'] > 24) {
            return date('y.m.d в H:i', $reg_date);
        }

        $key = ($time_distance['hours'] > 0) ? 'hours' : 'minutes';
        $measure_display = $time_distance[$key];
        $date_format = $time_declensions[$key];

        return declensionOfNouns($measure_display, $date_format) . ' назад';
    }
}