<?php
/**
 * Interactive map for coordinate selection (Leaflet + OSM).
 *
 * @var yii\web\View          $this
 * @var yii\widgets\ActiveForm $form
 * @var yii\db\ActiveRecord   $model
 * @var string                $latField   attribute name, default 'lat'
 * @var string                $lngField   attribute name, default 'lng'
 */

$latField = $latField ?? 'lat';
$lngField = $lngField ?? 'lng';

$lat     = (float)($model->$latField ?: 55.7558);
$lng     = (float)($model->$lngField ?: 37.6173);
$hasCoords = !empty($model->$latField) && !empty($model->$lngField);
$zoom    = $hasCoords ? 13 : 5;

$mapId      = 'map-' . $model->formName() . '-' . $latField;
$latId      = \yii\helpers\Html::getInputId($model, $latField);
$lngId      = \yii\helpers\Html::getInputId($model, $lngField);
$hasCoordsJs = $hasCoords ? 'true' : 'false';

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);

$this->registerJs(<<<JS
(function() {
    var lat = {$lat}, lng = {$lng}, hasCoords = {$hasCoordsJs};
    var latInput = document.getElementById('{$latId}');
    var lngInput = document.getElementById('{$lngId}');

    var map = L.map('{$mapId}').setView([lat, lng], {$zoom});
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    var marker = null;

    function placeMarker(latlng) {
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, {draggable: true}).addTo(map);
            marker.on('dragend', function(e) {
                updateInputs(e.target.getLatLng());
            });
        }
        updateInputs(latlng);
    }

    function updateInputs(latlng) {
        latInput.value = latlng.lat.toFixed(7);
        lngInput.value = latlng.lng.toFixed(7);
    }

    if ({$hasCoordsJs}) {
        placeMarker(L.latLng(lat, lng));
    }

    map.on('click', function(e) {
        placeMarker(e.latlng);
        map.panTo(e.latlng);
    });
})();
JS, \yii\web\View::POS_END);
?>

<div class="mb-3">
    <label class="form-label"><?= Yii::t('app', 'Location on map') ?> <span class="text-muted small"><?= Yii::t('app', '(click on map to set coordinates)') ?></span></label>
    <div id="<?= $mapId ?>" style="height:380px; border-radius:8px; border:1px solid #dee2e6; z-index:0;"></div>
</div>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, $latField)->textInput([
            'type'        => 'number',
            'step'        => 'any',
            'placeholder' => '55.7558',
            'id'          => $latId,
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, $lngField)->textInput([
            'type'        => 'number',
            'step'        => 'any',
            'placeholder' => '37.6173',
            'id'          => $lngId,
        ]) ?>
    </div>
</div>
