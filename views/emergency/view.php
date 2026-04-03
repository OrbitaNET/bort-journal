<?php
/** @var app\models\RescueService $model */
use yii\helpers\Html;
use app\models\RescueService;

$this->title = $model->name;
?>
<div class="row justify-content-center"><div class="col-lg-7">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= Html::encode($model->name) ?></h2>
    <div class="d-flex gap-2">
        <?= Html::a(Yii::t('app', 'Edit'), ['emergency/update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a('← ' . Yii::t('app', 'Back'), ['emergency/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>

<dl class="row">
    <dt class="col-sm-4"><?= Yii::t('app', 'Type') ?></dt>
    <dd class="col-sm-8"><?= Html::encode(RescueService::TYPES[$model->type] ?? $model->type) ?></dd>

    <?php if ($model->address): ?>
    <dt class="col-sm-4"><?= Yii::t('app', 'Address') ?></dt>
    <dd class="col-sm-8"><?= Html::encode($model->address) ?></dd>
    <?php endif ?>

    <?php if ($model->phone): ?>
    <dt class="col-sm-4"><?= Yii::t('app', 'Phone') ?></dt>
    <dd class="col-sm-8"><?= Html::encode($model->phone) ?></dd>
    <?php endif ?>

    <dt class="col-sm-4"><?= Yii::t('app', '24/7') ?></dt>
    <dd class="col-sm-8"><?= $model->is_24h ? Yii::t('app', 'Yes') : Yii::t('app', 'No') ?></dd>

    <?php if ($model->description): ?>
    <dt class="col-sm-4"><?= Yii::t('app', 'Description') ?></dt>
    <dd class="col-sm-8"><?= nl2br(Html::encode($model->description)) ?></dd>
    <?php endif ?>
</dl>

<?= $this->render('//shared/_poi_map_view', ['model' => $model]) ?>
</div></div>
