<?php

namespace app\controllers;

use Yii;
use app\models\FuelStation;
use app\models\Authority;
use app\models\Marina;
use app\models\MedicalPoint;
use app\models\ServiceStation;
use app\models\RescueService;
use app\models\MapPolygon;

class MapController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * AJAX: return all POI objects as GeoJSON-like array.
     */
    public function actionData()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $result = [];

        $types = [
            'fuel-station'   => [FuelStation::class,   '#f97316', Yii::t('app', 'Fuel stations')],
            'authority'      => [Authority::class,      '#8b5cf6', Yii::t('app', 'Authorities')],
            'marina'         => [Marina::class,         '#0ea5e9', Yii::t('app', 'Marinas')],
            'medical-point'  => [MedicalPoint::class,   '#22c55e', Yii::t('app', 'Medical facilities')],
            'service-station'=> [ServiceStation::class, '#eab308', Yii::t('app', 'Service stations')],
            'emergency'      => [RescueService::class,  '#ef4444', Yii::t('app', 'Rescue services')],
        ];

        foreach ($types as $key => [$class, $color, $label]) {
            $models = $class::find()->andWhere(['not', ['lat' => null]])->all();
            foreach ($models as $m) {
                $result[] = [
                    'type'       => $key,
                    'typeLabel'  => $label,
                    'color'      => $color,
                    'id'         => $m->id,
                    'name'       => $m->name,
                    'lat'        => (float)$m->lat,
                    'lng'        => (float)$m->lng,
                    'viewUrl'    => \yii\helpers\Url::to([$key . '/view', 'id' => $m->id]),
                    'editUrl'    => \yii\helpers\Url::to([$key . '/update', 'id' => $m->id]),
                ];
            }
        }

        return $result;
    }

    /**
     * AJAX: return all polygons.
     */
    public function actionPolygons()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return array_map(function ($p) {
            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'coordinates' => json_decode($p->coordinates, true),
                'color'       => $p->color,
            ];
        }, MapPolygon::find()->all());
    }

    /**
     * AJAX POST: save a new polygon.
     */
    public function actionSavePolygon()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        if (!$user || (!$user->isSuperadmin() && !$user->isAdmin())) {
            Yii::$app->response->statusCode = 403;
            return ['error' => 'Forbidden'];
        }

        $data = json_decode(Yii::$app->request->rawBody, true);

        $polygon = new MapPolygon();
        $polygon->name        = $data['name'] ?? Yii::t('app', 'New polygon');
        $polygon->coordinates = json_encode($data['coordinates']);
        $polygon->color       = $data['color'] ?? '#3388ff';

        if ($polygon->save()) {
            return ['id' => $polygon->id, 'name' => $polygon->name, 'color' => $polygon->color];
        }

        Yii::$app->response->statusCode = 422;
        return ['errors' => $polygon->errors];
    }

    /**
     * AJAX DELETE: delete polygon.
     */
    public function actionDeletePolygon($id)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $user = Yii::$app->user->identity;
        if (!$user || (!$user->isSuperadmin() && !$user->isAdmin())) {
            Yii::$app->response->statusCode = 403;
            return ['error' => 'Forbidden'];
        }

        $polygon = MapPolygon::findOne($id);
        if (!$polygon) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Not found'];
        }

        $polygon->delete();
        return ['ok' => true];
    }
}
