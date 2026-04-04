<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class UserController extends SuperadminController
{
    public function actionIndex()
    {
        $users = User::find()->orderBy(['created_at' => SORT_DESC])->all();

        return $this->render('index', ['users' => $users]);
    }

    public function actionView($id)
    {
        $user = $this->findUser($id);

        return $this->render('view', ['model' => $user]);
    }

    public function actionSetRole($id)
    {
        $user = $this->findUser($id);

        if ($user->isSuperadmin()) {
            throw new ForbiddenHttpException(Yii::t('app', 'Cannot change superadmin role.'));
        }

        $role = Yii::$app->request->post('role');
        $allowed = [User::ROLE_ADMIN, User::ROLE_USER];

        if (!in_array($role, $allowed, true)) {
            throw new \yii\web\BadRequestHttpException();
        }

        $auth = Yii::$app->authManager;
        $auth->revokeAll($user->id);
        $rbacRole = $auth->getRole($role);
        if ($rbacRole) {
            $auth->assign($rbacRole, $user->id);
        }

        $user->role = $role;
        $user->save(false);

        Yii::$app->session->setFlash('success', Yii::t('app', 'Role updated.'));

        return $this->redirect(['view', 'id' => $user->id]);
    }

    private function findUser($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException();
        }
        return $user;
    }
}
