<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use app\models\User;

/**
 * BaseController — base for all controllers requiring access control.
 *
 * Rules:
 *  - Guest        → redirect to login
 *  - Superadmin   → allow everything, no RBAC check
 *  - Admin / User → check RBAC permission "controller-id/action-id"
 */
class BaseController extends Controller
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

        /** @var User $identity */
        $identity = Yii::$app->user->identity;

        if ($identity->isSuperadmin()) {
            return true;
        }

        $permission = $action->controller->id . '/' . $action->id;

        if (!Yii::$app->user->can($permission)) {
            throw new ForbiddenHttpException('У вас нет доступа к этому разделу.');
        }

        return true;
    }
}
