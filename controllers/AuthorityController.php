<?php

namespace app\controllers;

use Yii;
use app\models\Authority;
use app\models\AuthorityOfficial;
use yii\web\NotFoundHttpException;

class AuthorityController extends PoiController
{
    protected $modelClass = Authority::class;

    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model'     => $model,
            'officials' => $model->officials,
        ]);
    }

    // --- Officials ---

    public function actionAddOfficial($authorityId)
    {
        $authority = Authority::findOne($authorityId);
        if (!$authority) throw new NotFoundHttpException();

        $official = new AuthorityOfficial(['authority_id' => $authorityId]);

        if ($official->load(Yii::$app->request->post()) && $official->save()) {
            return $this->redirect(['view', 'id' => $authorityId]);
        }

        return $this->render('official-form', [
            'model'     => $official,
            'authority' => $authority,
        ]);
    }

    public function actionUpdateOfficial($id)
    {
        $official = AuthorityOfficial::findOne($id);
        if (!$official) throw new NotFoundHttpException();

        if ($official->load(Yii::$app->request->post()) && $official->save()) {
            return $this->redirect(['view', 'id' => $official->authority_id]);
        }

        return $this->render('official-form', [
            'model'     => $official,
            'authority' => $official->authority,
        ]);
    }

    public function actionDeleteOfficial($id)
    {
        $official = AuthorityOfficial::findOne($id);
        if (!$official) throw new NotFoundHttpException();

        $authorityId = $official->authority_id;
        $official->delete();
        return $this->redirect(['view', 'id' => $authorityId]);
    }
}
