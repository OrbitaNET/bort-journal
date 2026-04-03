<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;

/**
 * Base CRUD controller for all POI entities.
 * Subclasses must define $modelClass.
 */
abstract class PoiController extends BaseController
{
    /** @var string FQCN of the model, e.g. app\models\FuelStation */
    protected $modelClass;

    public function actionIndex()
    {
        $models = ($this->modelClass)::find()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('index', ['models' => $models]);
    }

    public function actionView($id)
    {
        return $this->render('view', ['model' => $this->findModel($id)]);
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('form', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        $model = ($this->modelClass)::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException();
        }
        return $model;
    }
}
