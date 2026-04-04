<?php

namespace app\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use app\models\Application;
use app\models\ApplicationWaypoint;
use app\models\User;
use app\services\TelegramService;

/**
 * REST API controller.
 *
 * Authentication: Bearer token in Authorization header.
 *   Authorization: Bearer <access_token>
 *
 * Auth endpoints (no token required):
 *   POST /api/auth/register  — register new user
 *   POST /api/auth/login     — request Telegram code
 *   POST /api/auth/verify    — verify code, receive access_token
 *
 * Protected endpoints:
 *   GET  /api/poi/{type}     — list POI objects by type
 *   GET  /api/applications   — list current user's applications
 *   POST /api/applications   — create application
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    /** Actions that do not require Bearer token */
    private const PUBLIC_ACTIONS = ['register', 'login', 'verify'];

    public function behaviors()
    {
        return [
            'authenticator' => [
                'class'  => HttpBearerAuth::class,
                'except' => self::PUBLIC_ACTIONS,
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Auth — Register
    // -------------------------------------------------------------------------

    /**
     * Register a new user.
     * POST /api/auth/register
     *
     * Body: { "username": "...", "phone": "+79001234567" }
     *
     * After registration the user must link their Telegram account by sending
     * /start <phone> to the bot — only then they can log in.
     */
    public function actionRegister()
    {
        $body     = $this->parseJsonBody();
        $username = trim($body['username'] ?? '');
        $phone    = User::normalizePhone($body['phone'] ?? '');

        $errors = [];

        if ($username === '') {
            $errors['username'] = 'Username is required.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]{3,64}$/', $username)) {
            $errors['username'] = 'Username: 3–64 characters, letters, digits and _ only.';
        }

        if (!preg_match('/^\+\d{7,15}$/', $phone)) {
            $errors['phone'] = 'Enter a valid phone number (e.g. +79001234567).';
        }

        if (empty($errors)) {
            if (User::findByUsername($username)) {
                $errors['username'] = 'A user with this username already exists.';
            }
            if (User::findByPhone($phone)) {
                $errors['phone'] = 'An account with this phone number is already registered.';
            }
        }

        if (!empty($errors)) {
            return $this->asJson(['success' => false, 'errors' => $errors]);
        }

        $user           = new User();
        $user->username = $username;
        $user->phone    = $phone;
        $user->status   = User::STATUS_ACTIVE;

        if (!$user->save()) {
            return $this->asJson(['success' => false, 'errors' => $user->errors]);
        }

        $rbacRole = Yii::$app->authManager->getRole(User::ROLE_USER);
        if ($rbacRole) {
            Yii::$app->authManager->assign($rbacRole, $user->id);
        }

        $botUsername = (new TelegramService())->getBotUsername();

        return $this->asJson([
            'success' => true,
            'message' => "Account created. To link Telegram, send /start {$phone} to @{$botUsername}",
            'bot'     => $botUsername,
            'phone'   => $phone,
        ]);
    }

    // -------------------------------------------------------------------------
    // Auth — Login (request code)
    // -------------------------------------------------------------------------

    /**
     * Request a login code via Telegram.
     * POST /api/auth/login
     *
     * Body: { "phone": "+79001234567" }
     *
     * Sends a 4-digit code to the user's Telegram. Use /api/auth/verify next.
     */
    public function actionLogin()
    {
        $body  = $this->parseJsonBody();
        $phone = User::normalizePhone($body['phone'] ?? '');

        if (!preg_match('/^\+\d{7,15}$/', $phone)) {
            return $this->asJson(['success' => false, 'errors' => ['phone' => 'Enter a valid phone number.']]);
        }

        $user = User::findByPhone($phone);
        if (!$user) {
            return $this->asJson(['success' => false, 'errors' => ['phone' => 'Account with this number not found.']]);
        }
        if (!$user->telegram_id) {
            $botUsername = (new TelegramService())->getBotUsername();
            return $this->asJson([
                'success' => false,
                'errors'  => ['phone' => "Telegram not linked. Send /start {$phone} to @{$botUsername}"],
                'bot'     => $botUsername,
            ]);
        }

        $code    = $user->generateAuthCode();
        $service = new TelegramService();
        $sent    = $service->sendAuthCode($user->telegram_id, $code);

        if (!$sent) {
            return $this->asJson(['success' => false, 'errors' => ['telegram' => 'Could not send code to Telegram. Please try again.']]);
        }

        return $this->asJson([
            'success' => true,
            'message' => 'Code sent to your Telegram. Use /api/auth/verify to confirm.',
            'user_id' => $user->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Auth — Verify code
    // -------------------------------------------------------------------------

    /**
     * Verify Telegram code and receive access_token.
     * POST /api/auth/verify
     *
     * Body: { "user_id": 1, "code": "1234" }
     *
     * Returns: { "success": true, "access_token": "...", "user": {...} }
     */
    public function actionVerify()
    {
        $body   = $this->parseJsonBody();
        $userId = (int)($body['user_id'] ?? 0);
        $code   = trim($body['code'] ?? '');

        if (!$userId || !preg_match('/^\d{4}$/', $code)) {
            return $this->asJson(['success' => false, 'errors' => ['code' => 'user_id and 4-digit code are required.']]);
        }

        $user = User::findIdentity($userId);
        if (!$user || !$user->validateAuthCode($code)) {
            return $this->asJson(['success' => false, 'errors' => ['code' => 'Invalid or expired code.']]);
        }

        $user->clearAuthCode();

        // Generate access_token if not set
        if (empty($user->access_token)) {
            $user->access_token = Yii::$app->security->generateRandomString(40);
            $user->save(false);
        }

        return $this->asJson([
            'success'      => true,
            'access_token' => $user->access_token,
            'user'         => [
                'id'       => $user->id,
                'username' => $user->username,
                'phone'    => $user->phone,
                'role'     => $user->role,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // Current user
    // -------------------------------------------------------------------------

    /**
     * Get current user info.
     * GET /api/me
     */
    public function actionMe()
    {
        $this->requireAuth();

        /** @var \app\models\User $user */
        $user = Yii::$app->user->identity;

        return $this->asJson([
            'success' => true,
            'data'    => [
                'id'               => $user->id,
                'username'         => $user->username,
                'phone'            => $user->phone,
                'role'             => $user->role,
                'telegram_id'      => $user->telegram_id,
                'telegram_username'=> $user->telegram_username,
                'status'           => $user->status,
                'created_at'       => $user->created_at,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // POI
    // -------------------------------------------------------------------------

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
        $class   = $types[$type]['class'];
        $records = $class::find()->orderBy(['name' => SORT_ASC])->all();

        $data = array_map(fn($r) => $r->toArray(), $records);

        return $this->asJson(['success' => true, 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // Applications
    // -------------------------------------------------------------------------

    /**
     * List current user's applications.
     * GET /api/applications
     */
    public function actionApplications()
    {
        $this->requireAuth();

        $applications = Application::find()
            ->where(['creator_id' => Yii::$app->user->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('waypoints')
            ->all();

        $data = array_map(fn($app) => $this->serializeApplication($app), $applications);

        return $this->asJson(['success' => true, 'data' => $data]);
    }

    /**
     * Get single application.
     * GET /api/applications/{id}
     */
    public function actionApplication($id)
    {
        $this->requireAuth();
        $app = $this->findApplication($id);
        $app->populateRelation('waypoints', $app->getWaypoints()->all());
        return $this->asJson(['success' => true, 'data' => $this->serializeApplication($app)]);
    }

    /**
     * Update application (draft only for owner, any editable status for admin).
     * PUT /api/applications/{id}
     *
     * Body: same fields as create, plus optional "waypoints" array.
     */
    public function actionUpdateApplication($id)
    {
        $this->requireAuth();
        $app  = $this->findApplication($id);
        $user = Yii::$app->user->identity;

        if (!$app->canEdit($user->id, $user->isSuperadmin() || $user->isAdmin())) {
            return $this->asJson(['success' => false, 'errors' => ['access' => 'Forbidden.']]);
        }

        $body = $this->parseJsonBody();
        $app->load($body, '');

        if (!$app->save()) {
            return $this->asJson(['success' => false, 'errors' => $app->errors]);
        }

        if (array_key_exists('waypoints', $body)) {
            ApplicationWaypoint::deleteAll(['application_id' => $app->id]);
            if (is_array($body['waypoints'])) {
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
        }

        $app->refresh();
        return $this->asJson(['success' => true, 'data' => $this->serializeApplication($app)]);
    }

    /**
     * Delete application.
     * DELETE /api/applications/{id}
     */
    public function actionDeleteApplication($id)
    {
        $this->requireAuth();
        $app  = $this->findApplication($id);
        $user = Yii::$app->user->identity;

        if (!$app->canDelete($user->id, $user->isSuperadmin() || $user->isAdmin())) {
            return $this->asJson(['success' => false, 'errors' => ['access' => 'Forbidden.']]);
        }

        $app->delete();
        return $this->asJson(['success' => true]);
    }

    /**
     * Confirm application: draft → created.
     * POST /api/applications/{id}/confirm
     */
    public function actionConfirmApplication($id)
    {
        $this->requireAuth();
        $app  = $this->findApplication($id);
        $user = Yii::$app->user->identity;

        if ($app->creator_id !== $user->id && !$user->isSuperadmin() && !$user->isAdmin()) {
            return $this->asJson(['success' => false, 'errors' => ['access' => 'Forbidden.']]);
        }
        if ($app->status !== Application::STATUS_DRAFT) {
            return $this->asJson(['success' => false, 'errors' => ['status' => 'Application must be in draft status.']]);
        }

        $app->status = Application::STATUS_CREATED;
        $app->save(false);
        return $this->asJson(['success' => true, 'data' => $this->serializeApplication($app)]);
    }

    /**
     * Send PDF of confirmed application to current user's Telegram.
     * POST /api/applications/{id}/send-pdf
     *
     * Application must have status created, processing or processed.
     * Current user must have Telegram linked.
     */
    public function actionSendPdf($id)
    {
        $this->requireAuth();
        $app  = $this->findApplication($id);
        $user = Yii::$app->user->identity;

        $allowedStatuses = [
            Application::STATUS_CREATED,
            Application::STATUS_PROCESSING,
            Application::STATUS_PROCESSED,
        ];
        if (!in_array($app->status, $allowedStatuses)) {
            return $this->asJson(['success' => false, 'errors' => ['status' => 'Application must be confirmed to send PDF.']]);
        }

        if (!$user->telegram_id) {
            return $this->asJson(['success' => false, 'errors' => ['telegram' => 'No Telegram linked to your account.']]);
        }

        $app->populateRelation('waypoints', $app->getWaypoints()->all());

        $html   = $this->view->renderFile('@app/views/application/_pdf.php', ['model' => $app]);
        $tmpDir = Yii::getAlias('@runtime/mpdf');
        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode'         => 'utf-8',
            'format'       => 'A4',
            'tempDir'      => $tmpDir,
            'default_font' => 'dejavusans',
        ]);
        $mpdf->SetTitle('Заявка #' . $app->id);
        $mpdf->WriteHTML($html);

        $tmpFile = $tmpDir . '/app_' . $app->id . '_' . time() . '.pdf';
        $mpdf->Output($tmpFile, \Mpdf\Output\Destination::FILE);

        $telegram = new TelegramService();
        $sent     = $telegram->sendDocument($user->telegram_id, $tmpFile, 'Заявка #' . $app->id);
        @unlink($tmpFile);

        if (!$sent) {
            return $this->asJson(['success' => false, 'errors' => ['telegram' => 'Failed to send PDF to Telegram.']]);
        }

        return $this->asJson(['success' => true, 'message' => 'PDF sent to your Telegram.']);
    }

    /**
     * Send application to processing: created → processing.
     * POST /api/applications/{id}/send-processing
     */
    public function actionSendProcessing($id)
    {
        $this->requireAuth();
        $app  = $this->findApplication($id);
        $user = Yii::$app->user->identity;

        if ($app->creator_id !== $user->id && !$user->isSuperadmin() && !$user->isAdmin()) {
            return $this->asJson(['success' => false, 'errors' => ['access' => 'Forbidden.']]);
        }
        if ($app->status !== Application::STATUS_CREATED) {
            return $this->asJson(['success' => false, 'errors' => ['status' => 'Application must be in created status.']]);
        }

        $app->status = Application::STATUS_PROCESSING;
        $app->save(false);
        return $this->asJson(['success' => true, 'data' => $this->serializeApplication($app)]);
    }

    /**
     * Create application.
     * POST /api/applications
     *
     * Body (JSON):
     * {
     *   "start_lat": 55.7, "start_lng": 49.1, "start_address": "...",
     *   "end_lat":   55.8, "end_lng":   49.2, "end_address":   "...",
     *   "notes": "...",
     *   "waypoints": [
     *     {"poi_type": "fuel_station", "poi_id": 1, "sort_order": 0}
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
            return $this->asJson(['success' => false, 'errors' => $app->errors]);
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

        return $this->asJson(['success' => true, 'data' => $this->serializeApplication($app)]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function findApplication(int $id): Application
    {
        $user = Yii::$app->user->identity;
        $app  = Application::findOne($id);

        if (!$app) {
            throw new NotFoundHttpException("Application #{$id} not found.");
        }
        if ($app->creator_id !== $user->id && !$user->isSuperadmin() && !$user->isAdmin()) {
            throw new \yii\web\ForbiddenHttpException('Access denied.');
        }
        return $app;
    }

    private function requireAuth()
    {
        if (Yii::$app->user->isGuest) {
            throw new UnauthorizedHttpException('Требуется авторизация.');
        }
    }

    private function parseJsonBody(): array
    {
        $raw  = Yii::$app->request->rawBody;
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new BadRequestHttpException('Request body must be valid JSON.');
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
