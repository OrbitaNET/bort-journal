<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use app\models\Application;
use app\models\ApplicationWaypoint;

/**
 * REST API controller.
 *
 * Authentication: Bearer token in Authorization header.
 *   Authorization: Bearer <access_token>
 *
 * Endpoints:
 *   GET  /api/poi/{type}      — list POI objects by type
 *   GET  /api/applications    — list current user's applications
 *   POST /api/applications    — create application
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => HttpBearerAuth::class,
                'except' => [],
            ],
        ];
    }

    /**
     * List POI objects by type.
     * GET /api/poi/{type}
     *
     * @param string $type  fuel_station|marina|medical_point|rescue_service|service_station|authority
     */
    public function actionPoi($type)
    {
        $this->requireAuth();

        $types = ApplicationWaypoint::POI_TYPES;
        if (!isset($types[$type])) {
            throw new NotFoundHttpException("Unknown POI type: {$type}");
        }

        /** @var \app\models\PoiBase $class */
        $class = $types[$type]['class'];
        $records = $class::find()->orderBy(['name' => SORT_ASC])->all();

        $data = array_map(fn($r) => $r->toArray(), $records);

        return $this->asJson(['success' => true, 'data' => $data]);
    }

    /**
     * List current user's applications.
     * GET /api/applications
     */
    public function actionApplications()
    {
        $this->requireAuth();

        $userId = Yii::$app->user->id;
        $applications = Application::find()
            ->where(['creator_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('waypoints')
            ->all();

        $data = array_map(fn($app) => $this->serializeApplication($app), $applications);

        return $this->asJson(['success' => true, 'data' => $data]);
    }

    /**
     * Create application.
     * POST /api/applications
     *
     * Body (JSON):
     * {
     *   "start_lat": 55.7,
     *   "start_lng": 49.1,
     *   "start_address": "...",
     *   "end_lat": 55.8,
     *   "end_lng": 49.2,
     *   "end_address": "...",
     *   "notes": "...",
     *   "waypoints": [
     *     {"poi_type": "fuel_station", "poi_id": 1, "sort_order": 0},
     *     ...
     *   ]
     * }
     */
    public function actionCreateApplication()
    {
        $this->requireAuth();

        $body = $this->parseJsonBody();

        $app = new Application();
        $app->load($body, '');

        if (!$app->save()) {
            return $this->asJson([
                'success' => false,
                'errors'  => $app->errors,
            ]);
        }

        if (!empty($body['waypoints']) && is_array($body['waypoints'])) {
            foreach ($body['waypoints'] as $i => $wp) {
                $waypoint = new ApplicationWaypoint();
                $waypoint->application_id = $app->id;
                $waypoint->poi_type       = $wp['poi_type'] ?? '';
                $waypoint->poi_id         = (int)($wp['poi_id'] ?? 0);
                $waypoint->sort_order     = (int)($wp['sort_order'] ?? $i);

                if (!isset(ApplicationWaypoint::POI_TYPES[$waypoint->poi_type]) || !$waypoint->poi_id) {
                    continue;
                }
                $waypoint->save(false);
            }
        }

        $app->refresh();

        return $this->asJson([
            'success' => true,
            'data'    => $this->serializeApplication($app),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function requireAuth()
    {
        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException('Требуется авторизация.');
        }
    }

    private function parseJsonBody(): array
    {
        $raw = Yii::$app->request->rawBody;
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new BadRequestHttpException('Тело запроса должно быть валидным JSON.');
        }
        return $data;
    }

    private function serializeApplication(Application $app): array
    {
        return [
            'id'            => $app->id,
            'status'        => $app->status,
            'status_label'  => $app->getStatusLabel(),
            'start_lat'     => (float)$app->start_lat,
            'start_lng'     => (float)$app->start_lng,
            'start_address' => $app->start_address,
            'end_lat'       => (float)$app->end_lat,
            'end_lng'       => (float)$app->end_lng,
            'end_address'   => $app->end_address,
            'notes'         => $app->notes,
            'created_at'    => $app->created_at,
            'updated_at'    => $app->updated_at,
            'waypoints'     => array_map(fn($wp) => [
                'id'         => $wp->id,
                'poi_type'   => $wp->poi_type,
                'poi_id'     => $wp->poi_id,
                'sort_order' => $wp->sort_order,
            ], $app->waypoints),
        ];
    }
}
