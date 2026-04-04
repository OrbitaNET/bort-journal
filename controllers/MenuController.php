<?php

namespace app\controllers;

use Yii;
use app\models\MenuGroup;
use app\models\MenuItem;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class MenuController extends SuperadminController
{
    // -------------------------------------------------------------------------
    // Main page — list groups and ungrouped items
    // -------------------------------------------------------------------------

    public function actionIndex()
    {
        $groups = MenuGroup::find()
            ->with('items')
            ->orderBy(['sort_order' => SORT_ASC, 'name_en' => SORT_ASC])
            ->all();

        $ungrouped = MenuItem::find()
            ->where(['group_id' => null])
            ->orderBy(['sort_order' => SORT_ASC, 'label_en' => SORT_ASC])
            ->all();

        return $this->render('index', compact('groups', 'ungrouped'));
    }

    // -------------------------------------------------------------------------
    // Groups
    // -------------------------------------------------------------------------

    public function actionCreateGroup()
    {
        $model = new MenuGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('group-form', ['model' => $model]);
    }

    public function actionUpdateGroup($id)
    {
        $model = MenuGroup::findOne($id);
        if (!$model) throw new NotFoundHttpException();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('group-form', ['model' => $model]);
    }

    public function actionDeleteGroup($id)
    {
        $model = MenuGroup::findOne($id);
        if (!$model) throw new NotFoundHttpException();

        $model->delete();
        return $this->redirect(['index']);
    }

    // -------------------------------------------------------------------------
    // Items
    // -------------------------------------------------------------------------

    public function actionCreateItem()
    {
        $model = new MenuItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        $groups = MenuGroup::find()->orderBy(['sort_order' => SORT_ASC, 'name_en' => SORT_ASC])->all();
        return $this->render('item-form', ['model' => $model, 'groups' => $groups]);
    }

    public function actionUpdateItem($id)
    {
        $model = MenuItem::findOne($id);
        if (!$model) throw new NotFoundHttpException();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        $groups = MenuGroup::find()->orderBy(['sort_order' => SORT_ASC, 'name_en' => SORT_ASC])->all();
        return $this->render('item-form', ['model' => $model, 'groups' => $groups]);
    }

    public function actionDeleteItem($id)
    {
        $model = MenuItem::findOne($id);
        if (!$model) throw new NotFoundHttpException();

        $model->delete();
        return $this->redirect(['index']);
    }

    // -------------------------------------------------------------------------
    // Toggle item active state (AJAX)
    // -------------------------------------------------------------------------

    public function actionToggleItem($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = MenuItem::findOne($id);
        if (!$model) throw new NotFoundHttpException();

        $model->is_active = $model->is_active ? 0 : 1;
        $model->save(false);
        return ['is_active' => $model->is_active];
    }

    // -------------------------------------------------------------------------
    // Sort (AJAX drag-and-drop)
    // -------------------------------------------------------------------------

    public function actionSortGroups()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);
        foreach ($ids as $order => $id) {
            Yii::$app->db->createCommand()
                ->update('{{%menu_group}}', ['sort_order' => (int)$order], ['id' => (int)$id])
                ->execute();
        }
        return ['ok' => true];
    }

    public function actionSortItems()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $ids = Yii::$app->request->post('ids', []);
        foreach ($ids as $order => $id) {
            Yii::$app->db->createCommand()
                ->update('{{%menu_item}}', ['sort_order' => (int)$order], ['id' => (int)$id])
                ->execute();
        }
        return ['ok' => true];
    }
}
