<?php
/**
 * Read-only map showing a POI location.
 *
 * @var yii\web\View $this
 * @var float        $lat
 * @var float        $lng
 * @var string       $title
 */

$mapId = 'map-view-' . md5($lat . $lng . $title);

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);

$escapedTitle = \yii\helpers\Html::encode($title);

$this->registerJs(<<<JS
(function() {
    var map = L.map('{$mapId}', {scrollWheelZoom: false}).setView([{$lat}, {$lng}], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);
    L.marker([{$lat}, {$lng}]).addTo(map).bindPopup('{$escapedTitle}').openPopup();
})();
JS, \yii\web\View::POS_END);
?>
<div id="<?= $mapId ?>" style="height:320px; border-radius:8px; border:1px solid #dee2e6; margin-bottom:1.5rem; z-index:0;"></div>
