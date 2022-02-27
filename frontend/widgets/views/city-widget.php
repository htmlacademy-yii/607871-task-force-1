<?php

use yii\helpers\Html;
use \frontend\models\City;

/**
 * @var int $city_id
 */

?>
<div class="header__town">
    <?= Html::dropDownList('town[]', $city_id, City::getCityMap(),[
        'class' => 'multiple-select input town-select'
    ]); ?>
</div>
