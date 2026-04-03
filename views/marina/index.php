<?php
/** @var app\models\Marina[] $models */
echo $this->render('//shared/_poi_index', [
    'models'      => $models,
    'title'       => Yii::t('app', 'Marinas'),
    'createRoute' => 'marina/create',
    'viewRoute'   => 'marina/view',
    'updateRoute' => 'marina/update',
    'deleteRoute' => 'marina/delete',
    'columns'     => [
        ['attribute' => 'name',         'label' => Yii::t('app', 'Name')],
        ['attribute' => 'address',      'label' => Yii::t('app', 'Address')],
        ['attribute' => 'berths_count', 'label' => Yii::t('app', 'Berths count')],
        ['attribute' => 'phone',        'label' => Yii::t('app', 'Phone')],
    ],
]);
