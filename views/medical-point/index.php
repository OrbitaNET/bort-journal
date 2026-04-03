<?php
/** @var app\models\MedicalPoint[] $models */
echo $this->render('//shared/_poi_index', [
    'models'      => $models,
    'title'       => Yii::t('app', 'Medical facilities'),
    'createRoute' => 'medical-point/create',
    'viewRoute'   => 'medical-point/view',
    'updateRoute' => 'medical-point/update',
    'deleteRoute' => 'medical-point/delete',
    'columns'     => [
        ['attribute' => 'name',    'label' => Yii::t('app', 'Name')],
        ['attribute' => 'type',    'label' => Yii::t('app', 'Type')],
        ['attribute' => 'address', 'label' => Yii::t('app', 'Address')],
        ['attribute' => 'phone',   'label' => Yii::t('app', 'Phone')],
    ],
]);
