<?php
/** @var app\models\RescueService[] $models */
echo $this->render('//shared/_poi_index', [
    'models'      => $models,
    'title'       => Yii::t('app', 'Rescue services'),
    'createRoute' => 'emergency/create',
    'viewRoute'   => 'emergency/view',
    'updateRoute' => 'emergency/update',
    'deleteRoute' => 'emergency/delete',
    'columns'     => [
        ['attribute' => 'name',    'label' => Yii::t('app', 'Name')],
        ['attribute' => 'type',    'label' => Yii::t('app', 'Type')],
        ['attribute' => 'address', 'label' => Yii::t('app', 'Address')],
        ['attribute' => 'phone',   'label' => Yii::t('app', 'Phone')],
    ],
]);
