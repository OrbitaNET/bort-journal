<?php
/** @var app\models\Application $model */
use app\models\Application;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body        { font-family: dejavusans, sans-serif; font-size: 11pt; color: #222; }
    h1          { font-size: 16pt; margin-bottom: 4px; }
    h2          { font-size: 12pt; margin: 18px 0 6px; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
    .meta       { font-size: 9pt; color: #666; margin-bottom: 16px; }
    ol          { padding-left: 20px; margin: 0; }
    li          { margin-bottom: 14px; }
    .point-type { font-size: 9pt; font-weight: bold; color: #555; text-transform: uppercase; letter-spacing: .04em; }
    .point-name { font-size: 12pt; font-weight: bold; margin: 2px 0; }
    .point-detail { font-size: 9pt; color: #444; margin: 1px 0; }
    .point-start{ color: #1a7a2e; }
    .point-end  { color: #c0392b; }
    hr          { border: none; border-top: 1px solid #ddd; margin: 16px 0; }
</style>
</head>
<body>
<h1>Заявка #<?= $model->id ?></h1>
<div class="meta">
    Создана: <?= date('d.m.Y H:i', $model->created_at) ?>&nbsp;&nbsp;|&nbsp;&nbsp;
    Автор: <?= htmlspecialchars($model->creator->username ?? '—') ?>&nbsp;&nbsp;|&nbsp;&nbsp;
    Статус: <?= htmlspecialchars($model->statusLabel) ?>
</div>

<?php if ($model->notes): ?>
<p><strong>Примечания:</strong> <?= nl2br(htmlspecialchars($model->notes)) ?></p>
<hr>
<?php endif ?>

<h2>Маршрут</h2>
<ol>
    <!-- Start -->
    <li>
        <div class="point-type point-start">Начальная точка</div>
        <div class="point-name point-start"><?= htmlspecialchars($model->start_address ?: "{$model->start_lat}, {$model->start_lng}") ?></div>
        <div class="point-detail">Координаты: <?= $model->start_lat ?>, <?= $model->start_lng ?></div>
        <?php if ($model->start_address): ?>
        <div class="point-detail">Адрес: <?= htmlspecialchars($model->start_address) ?></div>
        <?php endif ?>
    </li>

    <!-- Waypoints -->
    <?php foreach ($model->waypoints as $wp): ?>
    <?php $poi = $wp->getPoi(); ?>
    <li>
        <div class="point-type"><?= htmlspecialchars($wp->poiTypeLabel) ?></div>
        <div class="point-name"><?= htmlspecialchars($poi ? $poi->name : '—') ?></div>
        <?php foreach ($wp->poiDetails as $label => $val): ?>
        <div class="point-detail"><?= htmlspecialchars($label) ?>: <?= htmlspecialchars($val) ?></div>
        <?php endforeach ?>
        <div class="point-detail">Координаты: <?= $poi ? "{$poi->lat}, {$poi->lng}" : '—' ?></div>
    </li>
    <?php endforeach ?>

    <!-- End -->
    <li>
        <div class="point-type point-end">Конечная точка</div>
        <div class="point-name point-end"><?= htmlspecialchars($model->end_address ?: "{$model->end_lat}, {$model->end_lng}") ?></div>
        <div class="point-detail">Координаты: <?= $model->end_lat ?>, <?= $model->end_lng ?></div>
        <?php if ($model->end_address): ?>
        <div class="point-detail">Адрес: <?= htmlspecialchars($model->end_address) ?></div>
        <?php endif ?>
    </li>
</ol>
</body>
</html>
