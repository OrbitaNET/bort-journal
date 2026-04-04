<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\ApplicationWaypoint;

/** @var yii\web\View $this */
/** @var app\models\Application $model */

$existingWaypoints = [];
foreach ($model->waypoints as $wp) {
    $poi = $wp->getPoi();
    $existingWaypoints[] = [
        'poi_type'  => $wp->poi_type,
        'poi_id'    => $wp->poi_id,
        'type_label'=> $wp->poiTypeLabel,
        'name'      => $poi ? $poi->name : '—',
        'address'   => $poi ? ($poi->address ?? '') : '',
    ];
}

$poiTypes    = ApplicationWaypoint::POI_TYPES;
$poiSearchUrl = Url::to(['application/poi-search']);

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js', ['position' => \yii\web\View::POS_END]);
?>

<?php $form = ActiveForm::begin(['id' => 'app-form']) ?>

<div class="row g-4">

<!-- ── Start point ─────────────────────────────────────── -->
<div class="col-12">
<div class="card">
<div class="card-header fw-semibold"><?= Yii::t('app', 'Start point') ?></div>
<div class="card-body">
    <div class="map-picker-wrap mb-2" id="map-start"></div>
    <div class="row g-2">
        <?= $form->field($model, 'start_lat', ['options' => ['class' => 'col-6']])->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'start_lng', ['options' => ['class' => 'col-6']])->hiddenInput()->label(false) ?>
    </div>
    <div class="mb-2">
        <label class="form-label small text-muted"><?= Yii::t('app', 'Coordinates') ?></label>
        <div id="start-coords-display" class="form-control-plaintext small">
            <?= $model->start_lat ? "{$model->start_lat}, {$model->start_lng}" : Yii::t('app', 'Click on map to choose') ?>
        </div>
    </div>
    <?= $form->field($model, 'start_address')->textInput(['placeholder' => Yii::t('app', 'Detected automatically...')]) ?>
</div>
</div>
</div>

<!-- ── End point ───────────────────────────────────────── -->
<div class="col-12">
<div class="card">
<div class="card-header fw-semibold"><?= Yii::t('app', 'End point') ?></div>
<div class="card-body">
    <div class="map-picker-wrap mb-2" id="map-end"></div>
    <div class="mb-2">
        <label class="form-label small text-muted"><?= Yii::t('app', 'Coordinates') ?></label>
        <div id="end-coords-display" class="form-control-plaintext small">
            <?= $model->end_lat ? "{$model->end_lat}, {$model->end_lng}" : Yii::t('app', 'Click on map to choose') ?>
        </div>
    </div>
    <?= $form->field($model, 'end_lat')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'end_lng')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'end_address')->textInput(['placeholder' => Yii::t('app', 'Detected automatically...')]) ?>
</div>
</div>
</div>

<!-- ── Intermediate waypoints ──────────────────────────── -->
<div class="col-12">
<div class="card">
<div class="card-header fw-semibold d-flex justify-content-between align-items-center">
    <?= Yii::t('app', 'Intermediate waypoints') ?>
    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-waypoint">
        + <?= Yii::t('app', 'Add object') ?>
    </button>
</div>
<div class="card-body p-2">
    <div id="waypoints-list" class="list-group list-group-flush">
        <!-- populated by JS -->
    </div>
    <p id="waypoints-empty" class="text-muted small p-2 mb-0"><?= Yii::t('app', 'No intermediate waypoints. Click «Add object» to add.') ?></p>
</div>
</div>
</div>

<!-- ── Notes ───────────────────────────────────────────── -->
<div class="col-12">
    <?= $form->field($model, 'notes')->textarea(['rows' => 3]) ?>
</div>

</div><!-- /row -->

<input type="hidden" name="waypoints_json" id="waypoints_json" value="">

<div class="mt-4 d-flex gap-2 flex-wrap">
    <?= Html::submitButton(Yii::t('app', 'Save draft'), ['class' => 'btn btn-secondary']) ?>
    <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-outline-secondary']) ?>
</div>

<?php ActiveForm::end() ?>

<!-- ── Waypoint picker modal ───────────────────────────── -->
<div class="modal fade" id="waypointModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= Yii::t('app', 'Add waypoint') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-5">
                        <select class="form-select form-select-sm" id="poi-type-filter">
                            <?php foreach ($poiTypes as $key => $info): ?>
                            <option value="<?= Html::encode($key) ?>"><?= Html::encode($info['label']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-7">
                        <input type="text" class="form-control form-control-sm" id="poi-search-input" placeholder="<?= Yii::t('app', 'Search by name...') ?>">
                    </div>
                </div>
                <div id="poi-results" class="list-group" style="max-height:350px;overflow-y:auto"></div>
            </div>
        </div>
    </div>
</div>

<?php
$existingJson = json_encode($existingWaypoints, JSON_UNESCAPED_UNICODE);
$noResults    = addslashes(Yii::t('app', 'Nothing found'));
$startLat     = (float)$model->start_lat ?: '';
$startLng     = (float)$model->start_lng ?: '';
$endLat       = (float)$model->end_lat   ?: '';
$endLng       = (float)$model->end_lng   ?: '';
$this->registerJs(<<<JS
(function () {
    // ── Leaflet map pickers ──────────────────────────────────────────────────
    // latName/lngName/addrName are the HTML name= attributes (not id)
    function initMapPicker(divId, latName, lngName, addrName, coordDisplay, initLat, initLng) {
        var lat = parseFloat(initLat) || 59.9343;
        var lng = parseFloat(initLng) || 30.3351;
        var map = L.map(divId).setView([lat, lng], initLat ? 14 : 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap', maxZoom: 19
        }).addTo(map);

        var marker = null;
        if (initLat && initLng) {
            marker = L.marker([lat, lng]).addTo(map);
        }

        map.on('click', function (e) {
            var ll = e.latlng;
            document.querySelector('[name="' + latName + '"]').value  = ll.lat.toFixed(7);
            document.querySelector('[name="' + lngName + '"]').value  = ll.lng.toFixed(7);
            document.getElementById(coordDisplay).textContent = ll.lat.toFixed(6) + ', ' + ll.lng.toFixed(6);

            if (marker) map.removeLayer(marker);
            marker = L.marker(ll).addTo(map);

            // Reverse geocode via Nominatim
            fetch('https://nominatim.openstreetmap.org/reverse?lat=' + ll.lat + '&lon=' + ll.lng + '&format=json')
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    document.querySelector('[name="' + addrName + '"]').value = data.display_name || '';
                });
        });
    }

    initMapPicker(
        'map-start',
        'Application[start_lat]', 'Application[start_lng]',
        'Application[start_address]', 'start-coords-display',
        '{$startLat}', '{$startLng}'
    );
    initMapPicker(
        'map-end',
        'Application[end_lat]', 'Application[end_lng]',
        'Application[end_address]', 'end-coords-display',
        '{$endLat}', '{$endLng}'
    );

    // ── Waypoints state ──────────────────────────────────────────────────────
    var waypoints = {$existingJson};

    function renderWaypoints() {
        var list = document.getElementById('waypoints-list');
        var empty = document.getElementById('waypoints-empty');
        list.innerHTML = '';

        if (waypoints.length === 0) {
            empty.style.display = '';
            document.getElementById('waypoints_json').value = '[]';
            return;
        }
        empty.style.display = 'none';

        waypoints.forEach(function (wp, i) {
            var item = document.createElement('div');
            item.className = 'list-group-item d-flex align-items-center gap-2 px-2 py-2';
            item.setAttribute('data-index', i);
            item.innerHTML =
                '<span class="drag-handle text-muted" style="cursor:grab;font-size:1.1rem">⠿</span>' +
                '<span class="badge bg-secondary">' + wp.type_label + '</span>' +
                '<span class="flex-grow-1"><strong>' + escHtml(wp.name) + '</strong>' +
                (wp.address ? '<br><small class="text-muted">' + escHtml(wp.address) + '</small>' : '') +
                '</span>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-wp" data-index="' + i + '">✕</button>';
            list.appendChild(item);
        });

        // Sortable
        Sortable.create(list, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function (evt) {
                var moved = waypoints.splice(evt.oldIndex, 1)[0];
                waypoints.splice(evt.newIndex, 0, moved);
                syncJson();
            }
        });

        syncJson();
    }

    function syncJson() {
        document.getElementById('waypoints_json').value = JSON.stringify(
            waypoints.map(function(w){ return { poi_type: w.poi_type, poi_id: w.poi_id }; })
        );
    }

    function escHtml(s) {
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    document.getElementById('waypoints-list').addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-remove-wp');
        if (!btn) return;
        var idx = parseInt(btn.getAttribute('data-index'));
        waypoints.splice(idx, 1);
        renderWaypoints();
    });

    renderWaypoints();

    // ── POI modal search ─────────────────────────────────────────────────────
    var modal = new bootstrap.Modal(document.getElementById('waypointModal'));
    document.getElementById('btn-add-waypoint').addEventListener('click', function () {
        modal.show();
        loadPois();
    });

    var searchTimer = null;
    document.getElementById('poi-search-input').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(loadPois, 300);
    });
    document.getElementById('poi-type-filter').addEventListener('change', loadPois);

    function loadPois() {
        var type = document.getElementById('poi-type-filter').value;
        var q    = document.getElementById('poi-search-input').value;
        fetch('{$poiSearchUrl}?type=' + encodeURIComponent(type) + '&q=' + encodeURIComponent(q))
            .then(function(r){ return r.json(); })
            .then(function(items) {
                var results = document.getElementById('poi-results');
                results.innerHTML = '';
                if (items.length === 0) {
                    results.innerHTML = '<div class="list-group-item text-muted small">{$noResults}</div>';
                    return;
                }
                var typeLabel = document.getElementById('poi-type-filter').selectedOptions[0].text;
                items.forEach(function(item) {
                    var a = document.createElement('button');
                    a.type = 'button';
                    a.className = 'list-group-item list-group-item-action';
                    a.innerHTML = '<strong>' + escHtml(item.name) + '</strong>' +
                        (item.address ? '<br><small class="text-muted">' + escHtml(item.address) + '</small>' : '');
                    a.addEventListener('click', function () {
                        var type = document.getElementById('poi-type-filter').value;
                        waypoints.push({
                            poi_type:   type,
                            poi_id:     item.id,
                            type_label: typeLabel,
                            name:       item.name,
                            address:    item.address,
                        });
                        renderWaypoints();
                        modal.hide();
                        document.getElementById('poi-search-input').value = '';
                    });
                    results.appendChild(a);
                });
            });
    }
})();
JS, \yii\web\View::POS_END);
?>
