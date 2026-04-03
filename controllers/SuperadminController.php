<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use app\models\User;

/**
 * Base controller for superadmin-only sections.
 */
class SuperadminController extends Controller
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

        if (!Yii::$app->user->identity->isSuperadmin()) {
            throw new ForbiddenHttpException('Доступ только для главного администратора.');
        }

        return true;
    }
}
