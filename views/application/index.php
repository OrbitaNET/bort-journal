<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Application;

$this->title = Yii::t('app', 'Applications');

$isAdmin = !Yii::$app->user->isGuest &&
    (Yii::$app->user->identity->isSuperadmin() || Yii::$app->user->identity->isAdmin());

$statuses = Application::statusLabels();
?>

<div class="page-header">
    <h2><?= Html::encode($this->title) ?></h2>
    <?= Html::a(Yii::t('app', 'New application'), ['create'], ['class' => 'btn btn-primary btn-sm']) ?>
</div>

<!-- Status filter -->
<div class="mb-3 d-flex flex-wrap gap-2">
    <?= Html::a(Yii::t('app', 'All'), ['index'],
        ['class' => 'btn btn-sm ' . (!$currentStatus ? 'btn-dark' : 'btn-outline-secondary')]) ?>
    <?php foreach ($statuses as $key => $label): ?>
        <?= Html::a($label, ['index', 'status' => $key],
            ['class' => 'btn btn-sm ' . ($currentStatus === $key ? 'btn-dark' : 'btn-outline-secondary')]) ?>
    <?php endforeach ?>
</div>

<?php if (empty($models)): ?>
<p class="text-muted"><?= Yii::t('app', 'No applications found.') ?></p>
<?php else: ?>

<!-- Desktop table -->
<div class="table-responsive-cards">
<table class="table table-hover align-middle desktop-table">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <?php if ($isAdmin): ?>
            <th><?= Yii::t('app', 'Creator') ?></th>
            <?php endif ?>
            <th><?= Yii::t('app', 'Status') ?></th>
            <th><?= Yii::t('app', 'Start address') ?></th>
            <th><?= Yii::t('app', 'End address') ?></th>
            <th><?= Yii::t('app', 'Created') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($models as $m): ?>
    <tr>
        <td><?= $m->id ?></td>
        <?php if ($isAdmin): ?>
        <td><?= Html::encode($m->creator->username ?? '—') ?></td>
        <?php endif ?>
        <td><span class="badge bg-<?= $m->statusBadgeClass ?>"><?= Html::encode($m->statusLabel) ?></span></td>
        <td class="text-truncate" style="max-width:180px"><?= Html::encode($m->start_address ?: "{$m->start_lat}, {$m->start_lng}") ?></td>
        <td class="text-truncate" style="max-width:180px"><?= Html::encode($m->end_address ?: "{$m->end_lat}, {$m->end_lng}") ?></td>
        <td><?= date('d.m.Y', $m->created_at) ?></td>
        <td>
            <?= Html::a(Yii::t('app', 'View'), ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        </td>
    </tr>
    <?php endforeach ?>
    </tbody>
</table>

<!-- Mobile cards -->
<div class="mobile-card-list">
<?php foreach ($models as $m): ?>
<div class="mobile-card-item">
    <div class="mci-title"><?= Yii::t('app', 'Application') ?> #<?= $m->id ?></div>
    <div class="mci-badge"><span class="badge bg-<?= $m->statusBadgeClass ?>"><?= Html::encode($m->statusLabel) ?></span></div>
    <div class="mci-meta">
        <?= Html::encode($m->start_address ?: "{$m->start_lat}, {$m->start_lng}") ?> →
        <?= Html::encode($m->end_address ?: "{$m->end_lat}, {$m->end_lng}") ?>
    </div>
    <?php if ($isAdmin): ?>
    <div class="mci-meta"><?= Yii::t('app', 'Creator') ?>: <?= Html::encode($m->creator->username ?? '—') ?></div>
    <?php endif ?>
    <div class="mci-meta"><?= date('d.m.Y', $m->created_at) ?></div>
    <div class="mci-actions">
        <?= Html::a(Yii::t('app', 'View'), ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>
<?php endforeach ?>
</div>
</div>

<?php endif ?>
