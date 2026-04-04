<?php
/** @var app\models\Authority[] $models */
echo $this->render('//shared/_poi_index', [
    'models'      => $models,
    'title'       => Yii::t('app', 'Administrative objects'),
    'createRoute' => 'authority/create',
    'viewRoute'   => 'authority/view',
    'updateRoute' => 'authority/update',
    'deleteRoute' => 'authority/delete',
    'columns'     => [
        ['attribute' => 'name',    'label' => Yii::t('app', 'Name')],
        ['attribute' => 'type',    'label' => Yii::t('app', 'Type')],
        ['attribute' => 'address', 'label' => Yii::t('app', 'Address')],
        ['attribute' => 'phone',   'label' => Yii::t('app', 'Phone')],
    ],
]);
