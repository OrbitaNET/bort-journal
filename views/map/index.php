<?php
use yii\helpers\Url;

$canEdit = !Yii::$app->user->isGuest &&
    (Yii::$app->user->identity->isSuperadmin() || Yii::$app->user->identity->isAdmin());

$this->title = Yii::t('app', 'Map');

$dataUrl       = Url::to(['map/data']);
$polygonsUrl   = Url::to(['map/polygons']);
$saveUrl       = Url::to(['map/save-polygon']);
$deleteUrl     = Url::to(['map/delete-polygon']);
$csrfToken     = Yii::$app->request->csrfToken;
$canEditJs     = $canEdit ? 'true' : 'false';
$deleteLabel   = addslashes(Yii::t('app', 'Delete'));
$deleteConfirm = addslashes(Yii::t('app', 'Delete polygon?'));
$viewLabel     = addslashes(Yii::t('app', 'View'));

$this->registerCssFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
$this->registerCssFile('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css');
$this->registerJsFile('https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', ['position' => \yii\web\View::POS_END]);
$this->registerJsFile('https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js', ['position' => \yii\web\View::POS_END]);

$this->registerJs(<<<JS
(function () {
    // ── Map init (Saint Petersburg) ──────────────────────────────
    var map = L.map('main-map').setView([59.9343, 30.3351], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);

    // ── Polygon color palette ─────────────────────────────────────
    var PALETTE = ['#e05c5c','#5c8fe0','#5cb85c','#e0a95c','#9b5ce0','#5cc4e0'];

    function pickColor(usedColors) {
        for (var i = 0; i < PALETTE.length; i++) {
            if (usedColors.indexOf(PALETTE[i]) === -1) return PALETTE[i];
        }
        return PALETTE[0];
    }

    function bboxOverlap(a, b) {
        return !(a.maxLat < b.minLat || a.minLat > b.maxLat ||
                 a.maxLng < b.minLng || a.minLng > b.maxLng);
    }

    function getBBox(coords) {
        var lats = coords.map(function(c){ return c[0]; });
        var lngs = coords.map(function(c){ return c[1]; });
        return { minLat: Math.min.apply(null,lats), maxLat: Math.max.apply(null,lats),
                 minLng: Math.min.apply(null,lngs), maxLng: Math.max.apply(null,lngs) };
    }

    // ── Polygon registry ──────────────────────────────────────────
    var polygonLayers = {};

    function addPolygonToMap(p) {
        var neighborColors = [];
        Object.values(polygonLayers).forEach(function(ex) {
            if (bboxOverlap(getBBox(p.coordinates), getBBox(ex.coords))) {
                neighborColors.push(ex.color);
            }
        });
        var color = p.color || pickColor(neighborColors);

        var layer = L.polygon(p.coordinates, {
            color: color, fillColor: color, fillOpacity: 0.25, weight: 2
        }).addTo(map);

        var canEdit = {$canEditJs};
        var popupContent = '<strong>' + p.name + '</strong>';
        if (canEdit) {
            popupContent += '<br><a href="#" class="del-poly text-danger small" data-id="' + p.id + '">{$deleteLabel}</a>';
        }
        layer.bindPopup(popupContent);
        layer.on('popupopen', function() {
            var el = document.querySelector('.del-poly[data-id="' + p.id + '"]');
            if (el) el.addEventListener('click', function(e) {
                e.preventDefault();
                removePolygon(p.id);
            });
        });

        polygonLayers[p.id] = { layer: layer, coords: p.coordinates, color: color, name: p.name };
    }

    function removePolygon(id) {
        if (!confirm('{$deleteConfirm}')) return;
        fetch('{$deleteUrl}/' + id, {
            method: 'POST',
            headers: { 'X-CSRF-Token': '{$csrfToken}', 'Content-Type': 'application/json' }
        }).then(function() {
            if (polygonLayers[id]) { map.removeLayer(polygonLayers[id].layer); delete polygonLayers[id]; }
        });
    }

    // Load existing polygons
    fetch('{$polygonsUrl}').then(function(r){ return r.json(); }).then(function(list) {
        list.forEach(addPolygonToMap);
    });

    // ── POI markers ───────────────────────────────────────────────
    var markerGroups = {};
    var typeLabels   = {};

    function makeIcon(color) {
        var svg = encodeURIComponent(
            '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="36" viewBox="0 0 24 36">' +
            '<path d="M12 0C5.37 0 0 5.37 0 12c0 9 12 24 12 24S24 21 24 12C24 5.37 18.63 0 12 0z" fill="' + color + '"/>' +
            '<circle cx="12" cy="12" r="5" fill="#fff"/>' +
            '</svg>'
        );
        return L.icon({
            iconUrl: 'data:image/svg+xml,' + svg,
            iconSize: [24, 36], iconAnchor: [12, 36], popupAnchor: [0, -36]
        });
    }

    fetch('{$dataUrl}').then(function(r){ return r.json(); }).then(function(items) {
        items.forEach(function(item) {
            if (!markerGroups[item.type]) {
                markerGroups[item.type] = L.layerGroup().addTo(map);
                typeLabels[item.type]   = { label: item.typeLabel, color: item.color };
            }
            var marker = L.marker([item.lat, item.lng], { icon: makeIcon(item.color) });
            marker.bindPopup('<strong>' + item.name + '</strong><br><a href="' + item.viewUrl + '">{$viewLabel}</a>');
            markerGroups[item.type].addLayer(marker);
        });

        // Filter UI
        var filterEl = document.getElementById('filter-items');
        if (Object.keys(typeLabels).length > 0) {
            document.getElementById('map-filter').style.display = '';
        }
        Object.keys(typeLabels).forEach(function(type) {
            var info = typeLabels[type];
            var label = document.createElement('label');
            label.innerHTML = '<input type="checkbox" checked data-type="' + type + '"> ' +
                '<span class="dot" style="background:' + info.color + '"></span>' + info.label;
            label.querySelector('input').addEventListener('change', function(e) {
                if (e.target.checked) map.addLayer(markerGroups[type]);
                else map.removeLayer(markerGroups[type]);
            });
            filterEl.appendChild(label);
        });
    });

    // ── Draw mode (admin/superadmin only) ─────────────────────────
    var canEdit = {$canEditJs};
    if (!canEdit) return;

    var drawnItems  = new L.FeatureGroup().addTo(map);
    var drawHandler = new L.Draw.Polygon(map, {
        shapeOptions: { color: '#3388ff', fillOpacity: 0.2 },
        showArea: false
    });

    var pendingLayer = null;

    document.getElementById('btn-draw').addEventListener('click', function() {
        this.style.display = 'none';
        document.getElementById('btn-cancel-draw').style.display = '';
        drawHandler.enable();
    });

    document.getElementById('btn-cancel-draw').addEventListener('click', function() {
        this.style.display = 'none';
        document.getElementById('btn-draw').style.display = '';
        drawHandler.disable();
        if (pendingLayer) { drawnItems.removeLayer(pendingLayer); pendingLayer = null; }
    });

    map.on(L.Draw.Event.CREATED, function(e) {
        drawHandler.disable();
        pendingLayer = e.layer;
        drawnItems.addLayer(pendingLayer);

        var latlngs = pendingLayer.getLatLngs()[0];
        var modal = document.getElementById('polygon-modal-bg');
        modal.classList.add('show');
        document.getElementById('polygon-name-input').value = '';
        document.getElementById('polygon-name-input').focus();

        document.getElementById('btn-modal-cancel').onclick = function() {
            modal.classList.remove('show');
            if (pendingLayer) { drawnItems.removeLayer(pendingLayer); pendingLayer = null; }
            document.getElementById('btn-cancel-draw').click();
        };

        document.getElementById('btn-modal-save').onclick = function() {
            var name = document.getElementById('polygon-name-input').value.trim();
            if (!name) return;
            modal.classList.remove('show');

            var coords = latlngs.map(function(ll) { return [ll.lat, ll.lng]; });
            var neighborColors = Object.values(polygonLayers).filter(function(p) {
                return bboxOverlap(getBBox(coords), getBBox(p.coords));
            }).map(function(p){ return p.color; });
            var color = pickColor(neighborColors);

            fetch('{$saveUrl}', {
                method: 'POST',
                headers: { 'X-CSRF-Token': '{$csrfToken}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: name, coordinates: coords, color: color })
            }).then(function(r){ return r.json(); }).then(function(saved) {
                if (pendingLayer) { drawnItems.removeLayer(pendingLayer); pendingLayer = null; }
                addPolygonToMap({ id: saved.id, name: saved.name, coordinates: coords, color: saved.color });
                document.getElementById('btn-cancel-draw').click();
            });
        };
    });
})();
JS, \yii\web\View::POS_END);
?>

<style>
#main-map { height: calc(100vh - 57px); width: 100%; }
#map-filter {
    position: absolute; top: 70px; left: 10px; z-index: 1000;
    background: #fff; border-radius: 8px; padding: 12px 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,.2); min-width: 190px;
}
#map-filter h6 { margin: 0 0 8px; font-size: .8rem; text-transform: uppercase; color: #6c757d; }
#map-filter label { display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: .9rem; margin-bottom: 4px; }
#map-filter .dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
#edit-toolbar {
    position: absolute; top: 70px; right: 10px; z-index: 1000;
    background: #fff; border-radius: 8px; padding: 10px 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
}
#polygon-modal-bg {
    display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 9999;
    align-items: center; justify-content: center;
}
#polygon-modal-bg.show { display: flex; }
#polygon-modal { background: #fff; border-radius: 10px; padding: 24px; min-width: 320px; }
</style>

<div style="position:relative;">
    <div id="main-map"></div>

    <div id="map-filter" style="display:none">
        <h6><?= Yii::t('app', 'Filter') ?></h6>
        <div id="filter-items"></div>
    </div>

    <?php if ($canEdit): ?>
    <div id="edit-toolbar">
        <button id="btn-draw" class="btn btn-sm btn-outline-primary"><?= Yii::t('app', 'Draw polygon') ?></button>
        <button id="btn-cancel-draw" class="btn btn-sm btn-outline-secondary" style="display:none"><?= Yii::t('app', 'Cancel') ?></button>
    </div>
    <?php endif ?>
</div>

<div id="polygon-modal-bg">
    <div id="polygon-modal">
        <h5 class="mb-3"><?= Yii::t('app', 'Polygon name') ?></h5>
        <input type="text" id="polygon-name-input" class="form-control mb-3" placeholder="<?= Yii::t('app', 'Enter name...') ?>">
        <div class="d-flex gap-2 justify-content-end">
            <button class="btn btn-secondary btn-sm" id="btn-modal-cancel"><?= Yii::t('app', 'Cancel') ?></button>
            <button class="btn btn-primary btn-sm" id="btn-modal-save"><?= Yii::t('app', 'Save') ?></button>
        </div>
    </div>
</div>
