<?php
/** @var yii\web\View $this */
/** @var app\models\FuelStation[] $models */
echo $this->render('//shared/_poi_index', [
    'models'       => $models,
    'title'        => Yii::t('app', 'Fuel stations'),
    'createRoute'  => 'fuel-station/create',
    'viewRoute'    => 'fuel-station/view',
    'updateRoute'  => 'fuel-station/update',
    'deleteRoute'  => 'fuel-station/delete',
    'columns'      => [
        ['attribute' => 'name',        'label' => Yii::t('app', 'Name')],
        ['attribute' => 'address',     'label' => Yii::t('app', 'Address')],
        ['attribute' => 'fuel_types',  'label' => Yii::t('app', 'Fuel types')],
        ['attribute' => 'phone',       'label' => Yii::t('app', 'Phone')],
    ],
]);
