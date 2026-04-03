<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionLanguage($lang)
    {
        Yii::$app->session->set('language', $lang);
        $referrer = Yii::$app->request->referrer ?: Yii::$app->homeUrl;
        return $this->redirect($referrer);
    }
}
