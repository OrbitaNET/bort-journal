<?php
/** @var app\models\ServiceStation[] $models */
echo $this->render('//shared/_poi_index', [
    'models'      => $models,
    'title'       => Yii::t('app', 'Service stations'),
    'createRoute' => 'service-station/create',
    'viewRoute'   => 'service-station/view',
    'updateRoute' => 'service-station/update',
    'deleteRoute' => 'service-station/delete',
    'columns'     => [
        ['attribute' => 'name',    'label' => Yii::t('app', 'Name')],
        ['attribute' => 'address', 'label' => Yii::t('app', 'Address')],
        ['attribute' => 'phone',   'label' => Yii::t('app', 'Phone')],
    ],
]);
