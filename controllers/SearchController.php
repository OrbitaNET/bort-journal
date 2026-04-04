<?php

namespace app\controllers;

use Yii;
use app\models\FuelStation;
use app\models\Authority;
use app\models\Marina;
use app\models\MedicalPoint;
use app\models\ServiceStation;
use app\models\RescueService;

class SearchController extends BaseController
{
    public function actionIndex()
    {
        $q = trim(Yii::$app->request->get('q', ''));

        $results = [];

        if ($q !== '') {
            $types = [
                'fuel-station'    => [FuelStation::class,    Yii::t('app', 'Fuel stations')],
                'authority'       => [Authority::class,       Yii::t('app', 'Administrative objects')],
                'marina'          => [Marina::class,          Yii::t('app', 'Marinas')],
                'medical-point'   => [MedicalPoint::class,    Yii::t('app', 'Medical facilities')],
                'service-station' => [ServiceStation::class,  Yii::t('app', 'Service stations')],
                'emergency'       => [RescueService::class,   Yii::t('app', 'Rescue services')],
            ];

            foreach ($types as $route => [$class, $label]) {
                $models = $class::find()
                    ->andWhere(['or',
                        ['like', 'name',    $q],
                        ['like', 'address', $q],
                    ])
                    ->orderBy(['name' => SORT_ASC])
                    ->all();

                if ($models) {
                    $results[] = [
                        'label'   => $label,
                        'route'   => $route,
                        'models'  => $models,
                    ];
                }
            }
        }

        return $this->render('index', [
            'q'       => $q,
            'results' => $results,
        ]);
    }
}
