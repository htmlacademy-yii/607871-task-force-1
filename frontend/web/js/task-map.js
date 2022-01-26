$(function () {
    $node = $('#map');

    ymaps.ready(init);
    function init(){
        // Создание карты.
        var myMap = new ymaps.Map("map", {
            // Координаты центра карты.
            // Порядок по умолчанию: «широта, долгота».
            // Чтобы не определять координаты центра карты вручную,
            // воспользуйтесь инструментом Определение координат.
            center: [$node.data('lat'), $node.data('lon')],
    // Уровень масштабирования. Допустимые значения:
    // от 0 (весь мир) до 19.
    zoom: 15
    });

    var myPlacemark = new ymaps.Placemark([$node.data('lat'), $node.data('lon')], {}, {
        preset: 'islands#redIcon'
    })
    myMap.geoObjects.add(myPlacemark);
    }
});