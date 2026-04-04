<?php
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Application;

$this->title = Yii::t('app', 'Application') . ' #' . $model->id;

$uid     = Yii::$app->user->id;
$isAdmin = !Yii::$app->user->isGuest &&
    (Yii::$app->user->identity->isSuperadmin() || Yii::$app->user->identity->isAdmin());
$isOwner = $model->creator_id == $uid;
?>

<div class="page-header">
    <h2><?= Html::encode($this->title) ?></h2>
    <div class="detail-actions">
        <?php if ($model->canEdit($uid, $isAdmin)): ?>
            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
        <?php endif ?>

        <?php if ($model->status === Application::STATUS_DRAFT && ($isOwner || $isAdmin)): ?>
            <?= Html::a(Yii::t('app', 'Confirm'), ['confirm', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-primary',
                'data-confirm' => Yii::t('app', 'Confirm this application?'),
            ]) ?>
        <?php endif ?>

        <?php if ($model->status === Application::STATUS_CREATED && ($isOwner || $isAdmin)): ?>
            <?= Html::a(Yii::t('app', 'Send to processing'), ['send-processing', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-warning',
                'data-confirm' => Yii::t('app', 'Send to processing?'),
            ]) ?>
            <?= Html::a(Yii::t('app', 'Send PDF to me'), ['send-pdf', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-info',
                'data-confirm' => Yii::t('app', 'Send PDF via Telegram?'),
            ]) ?>
        <?php endif ?>

        <?php if ($model->status === Application::STATUS_PROCESSING && $isAdmin): ?>
            <?= Html::a(Yii::t('app', 'Mark as processed + send PDF'), ['process', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-success',
                'data-method' => 'post',
                'data-confirm' => Yii::t('app', 'Mark as processed and send PDF to creator?'),
            ]) ?>
        <?php endif ?>

        <?php if ($model->canDelete($uid, $isAdmin)): ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-sm btn-outline-danger',
                'data-method' => 'post',
                'data-confirm' => Yii::t('app', 'Delete this application?'),
            ]) ?>
        <?php endif ?>

        <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
    </div>
</div>

<?php if (Yii::$app->session->hasFlash('success')): ?>
<div class="alert alert-success"><?= Html::encode(Yii::$app->session->getFlash('success')) ?></div>
<?php endif ?>
<?php if (Yii::$app->session->hasFlash('error')): ?>
<div class="alert alert-danger"><?= Html::encode(Yii::$app->session->getFlash('error')) ?></div>
<?php endif ?>

<div class="row g-4">

<!-- Status & meta -->
<div class="col-12 col-md-4">
<div class="card">
<div class="card-header fw-semibold"><?= Yii::t('app', 'Info') ?></div>
<div class="card-body">
<dl class="mb-0">
    <dt><?= Yii::t('app', 'Status') ?></dt>
    <dd><span class="badge bg-<?= $model->statusBadgeClass ?>"><?= Html::encode($model->statusLabel) ?></span></dd>

    <dt><?= Yii::t('app', 'Creator') ?></dt>
    <dd><?= Html::encode($model->creator->username ?? '—') ?></dd>

    <dt><?= Yii::t('app', 'Created') ?></dt>
    <dd><?= date('d.m.Y H:i', $model->created_at) ?></dd>

    <?php if ($model->notes): ?>
    <dt><?= Yii::t('app', 'Notes') ?></dt>
    <dd><?= nl2br(Html::encode($model->notes)) ?></dd>
    <?php endif ?>
</dl>
</div>
</div>
</div>

<!-- Route -->
<div class="col-12 col-md-8">
<div class="card">
<div class="card-header fw-semibold"><?= Yii::t('app', 'Route') ?></div>
<div class="card-body p-0">
<ol class="list-group list-group-numbered list-group-flush">

    <!-- Start -->
    <li class="list-group-item">
        <div class="fw-semibold text-success"><?= Yii::t('app', 'Start point') ?></div>
        <div class="small text-muted"><?= Html::encode("{$model->start_lat}, {$model->start_lng}") ?></div>
        <?php if ($model->start_address): ?>
        <div class="small"><?= Html::encode($model->start_address) ?></div>
        <?php endif ?>
    </li>

    <!-- Waypoints -->
    <?php foreach ($model->waypoints as $wp): ?>
    <?php $poi = $wp->getPoi(); ?>
    <li class="list-group-item">
        <span class="badge bg-secondary me-1"><?= Html::encode($wp->poiTypeLabel) ?></span>
        <span class="fw-semibold"><?= Html::encode($poi ? $poi->name : '—') ?></span>
        <?php foreach ($wp->poiDetails as $label => $val): ?>
        <div class="small text-muted"><?= Html::encode($label) ?>: <?= Html::encode($val) ?></div>
        <?php endforeach ?>
    </li>
    <?php endforeach ?>

    <!-- End -->
    <li class="list-group-item">
        <div class="fw-semibold text-danger"><?= Yii::t('app', 'End point') ?></div>
        <div class="small text-muted"><?= Html::encode("{$model->end_lat}, {$model->end_lng}") ?></div>
        <?php if ($model->end_address): ?>
        <div class="small"><?= Html::encode($model->end_address) ?></div>
        <?php endif ?>
    </li>

</ol>
</div>
</div>
</div>

</div>
