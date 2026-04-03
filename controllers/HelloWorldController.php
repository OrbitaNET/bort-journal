<?php

namespace app\controllers;

use Yii;

class HelloWorldController extends BaseController
{
    public function actionIndex()
    {
        return $this->render('index', [
            'username' => Yii::$app->user->identity->username,
        ]);
    }
}
