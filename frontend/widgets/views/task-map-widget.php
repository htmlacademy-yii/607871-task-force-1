<?php
/**
 * @var \frontend\models\Task $task
 */
?>
<?php if ($task->latitude && $task->longitude): ?>
<script type="text/javascript">
    ymaps.ready(init);
    function init(){
        // Создание карты.
        var myMap = new ymaps.Map("map", {
            // Координаты центра карты.
            // Порядок по умолчанию: «широта, долгота».
            // Чтобы не определять координаты центра карты вручную,
            // воспользуйтесь инструментом Определение координат.
            center: [<?= "{$task->latitude}, {$task->longitude}"; ?>],
            // Уровень масштабирования. Допустимые значения:
            // от 0 (весь мир) до 19.
            zoom: 15
        });

        var myPlacemark = new ymaps.Placemark([<?= "{$task->latitude}, {$task->longitude}"; ?>], {}, {
            preset: 'islands#redIcon'
        })
        myMap.geoObjects.add(myPlacemark);
    }

</script>
<?php endif; ?>