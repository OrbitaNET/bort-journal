<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Application;
use app\models\ApplicationWaypoint;
use app\services\TelegramService;

class ApplicationController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->loginRequired();
            return false;
        }
        return true;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function isAdmin()
    {
        $id = Yii::$app->user->identity;
        return $id->isSuperadmin() || $id->isAdmin();
    }

    private function findModel($id)
    {
        $model = Application::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $model;
    }

    private function canView(Application $model)
    {
        return $this->isAdmin() || $model->creator_id == Yii::$app->user->id;
    }

    // ── Save waypoints from JSON hidden field ────────────────────────────────

    private function saveWaypoints(Application $app, $waypointsJson)
    {
        ApplicationWaypoint::deleteAll(['application_id' => $app->id]);
        $items = json_decode($waypointsJson, true);
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $i => $item) {
            if (empty($item['poi_type']) || empty($item['poi_id'])) {
                continue;
            }
            if (!isset(ApplicationWaypoint::POI_TYPES[$item['poi_type']])) {
                continue;
            }
            $wp = new ApplicationWaypoint();
            $wp->application_id = $app->id;
            $wp->poi_type       = $item['poi_type'];
            $wp->poi_id         = (int)$item['poi_id'];
            $wp->sort_order     = $i;
            $wp->save(false);
        }
    }

    // ── PDF generation ───────────────────────────────────────────────────────

    private function generatePdf(Application $model)
    {
        $html = $this->renderPartial('_pdf', ['model' => $model]);

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
        $mpdf->SetTitle('Заявка #' . $model->id);
        $mpdf->WriteHTML($html);

        $tmpFile = $tmpDir . '/app_' . $model->id . '_' . time() . '.pdf';
        $mpdf->Output($tmpFile, \Mpdf\Output\Destination::FILE);
        return $tmpFile;
    }

    // ── Actions ──────────────────────────────────────────────────────────────

    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $query  = Application::find()->with('creator')->orderBy(['created_at' => SORT_DESC]);

        if (!$this->isAdmin()) {
            $query->where(['creator_id' => Yii::$app->user->id]);
        }

        if ($status) {
            $query->andWhere(['status' => $status]);
        }

        $models = $query->all();

        return $this->render('index', [
            'models'        => $models,
            'currentStatus' => $status,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (!$this->canView($model)) {
            throw new ForbiddenHttpException();
        }
        $model->populateRelation('waypoints', $model->getWaypoints()->all());
        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate()
    {
        $model = new Application();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->saveWaypoints($model, Yii::$app->request->post('waypoints_json', '[]'));
            Yii::$app->session->setFlash('success', Yii::t('app', 'Draft saved.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!$model->canEdit(Yii::$app->user->id, $this->isAdmin())) {
            throw new ForbiddenHttpException();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->saveWaypoints($model, Yii::$app->request->post('waypoints_json', '[]'));
            Yii::$app->session->setFlash('success', Yii::t('app', 'Saved.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->populateRelation('waypoints', $model->getWaypoints()->all());
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->canDelete(Yii::$app->user->id, $this->isAdmin())) {
            throw new ForbiddenHttpException();
        }
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Deleted.'));
        return $this->redirect(['index']);
    }

    /** Draft → Created */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        if (!$this->canView($model) || $model->status !== Application::STATUS_DRAFT) {
            throw new ForbiddenHttpException();
        }
        $model->status = Application::STATUS_CREATED;
        $model->save(false);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Application confirmed.'));
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /** Created → Processing */
    public function actionSendProcessing($id)
    {
        $model = $this->findModel($id);
        if (!$this->canView($model) || $model->status !== Application::STATUS_CREATED) {
            throw new ForbiddenHttpException();
        }
        $model->status = Application::STATUS_PROCESSING;
        $model->save(false);
        Yii::$app->session->setFlash('success', Yii::t('app', 'Sent to processing.'));
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /** Send PDF to creator's Telegram (Created/Processing → Sent) */
    public function actionSendPdf($id)
    {
        $model = $this->findModel($id);
        if (!$this->canView($model)) {
            throw new ForbiddenHttpException();
        }
        if (!in_array($model->status, [Application::STATUS_CREATED, Application::STATUS_PROCESSING])) {
            throw new ForbiddenHttpException();
        }

        $creator = $model->creator;
        if (!$creator || !$creator->telegram_id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'User has no Telegram linked.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->populateRelation('waypoints', $model->getWaypoints()->all());
        $tmpFile = $this->generatePdf($model);

        $telegram = new TelegramService();
        $telegram->sendDocument($creator->telegram_id, $tmpFile, 'Заявка #' . $model->id);
        @unlink($tmpFile);

        $model->status = Application::STATUS_SENT;
        $model->save(false);

        Yii::$app->session->setFlash('success', Yii::t('app', 'PDF sent to Telegram.'));
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /** Admin: Processing → Processed + send PDF to creator */
    public function actionProcess($id)
    {
        if (!$this->isAdmin()) {
            throw new ForbiddenHttpException();
        }
        $model = $this->findModel($id);
        if ($model->status !== Application::STATUS_PROCESSING) {
            throw new ForbiddenHttpException();
        }

        // Load updated fields if submitted
        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            $this->saveWaypoints($model, Yii::$app->request->post('waypoints_json', '[]'));
        }

        $model->status = Application::STATUS_PROCESSED;
        $model->save(false);

        $creator = $model->creator;
        if ($creator && $creator->telegram_id) {
            $model->populateRelation('waypoints', $model->getWaypoints()->all());
            $tmpFile = $this->generatePdf($model);
            $telegram = new TelegramService();
            $telegram->sendDocument($creator->telegram_id, $tmpFile, 'Заявка #' . $model->id . ' обработана');
            @unlink($tmpFile);
        }

        Yii::$app->session->setFlash('success', Yii::t('app', 'Application processed and PDF sent.'));
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /** AJAX: search POIs for waypoint picker */
    public function actionPoiSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $type  = Yii::$app->request->get('type', '');
        $query = Yii::$app->request->get('q', '');

        if (!isset(ApplicationWaypoint::POI_TYPES[$type])) {
            return [];
        }

        $class  = ApplicationWaypoint::POI_TYPES[$type]['class'];
        $models = $class::find()
            ->andFilterWhere(['like', 'name', $query])
            ->orderBy(['name' => SORT_ASC])
            ->limit(30)
            ->all();

        $result = [];
        foreach ($models as $m) {
            $result[] = [
                'id'      => $m->id,
                'name'    => $m->name,
                'address' => $m->address ?? '',
                'lat'     => $m->lat,
                'lng'     => $m->lng,
            ];
        }
        return $result;
    }
}
