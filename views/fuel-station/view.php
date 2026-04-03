<?php
/** @var yii\web\View $this */
/** @var app\models\FuelStation $model */
use yii\helpers\Html;

$this->title = $model->name;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?= Html::encode($model->name) ?></h2>
    <div class="d-flex gap-2">
        <?= Html::a(Yii::t('app', 'Edit'), ['fuel-station/update', 'id' => $model->id], ['class' => 'btn btn-outline-primary btn-sm']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['fuel-station/delete', 'id' => $model->id], ['class' => 'btn btn-outline-danger btn-sm', 'data-confirm' => Yii::t('app', 'Are you sure?'), 'data-method' => 'post']) ?>
        <?= Html::a('← ' . Yii::t('app', 'Back'), ['fuel-station/index'], ['class' => 'btn btn-outline-secondary btn-sm']) ?>
    </div>
</div>
<?= $this->render('//shared/_poi_map_view', ['lat' => $model->lat, 'lng' => $model->lng, 'title' => $model->name]) ?>
<div class="card"><div class="card-body">
<?= $this->render('//shared/_poi_detail', ['model' => $model, 'rows' => [
    Yii::t('app', 'Latitude')     => $model->lat,
    Yii::t('app', 'Longitude')    => $model->lng,
    Yii::t('app', 'Address')      => $model->address,
    Yii::t('app', 'Phone')        => $model->phone,
    Yii::t('app', 'Fuel types')   => $model->fuel_types,
    Yii::t('app', 'Working hours')=> $model->working_hours,
    Yii::t('app', 'Description')  => $model->description,
]]) ?>
</div></div>
