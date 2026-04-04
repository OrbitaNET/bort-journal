<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'auth/login',
    'homeUrl' => ['map/index'],
    'language' => 'ru',
    'sourceLanguage' => 'en',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'rjW8nN5OxhjSVTaCeaN9aMlU9FZA_2Ri',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass'       => 'app\models\User',
            'enableAutoLogin'     => true,
            'loginUrl'            => ['auth/login'],
            'authTimeout'         => null,
            'absoluteAuthTimeout' => 3600 * 24,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'i18n' => [
            'translations' => [
                'app' => [
                    'class'          => 'yii\i18n\PhpMessageSource',
                    'basePath'       => '@app/messages',
                    'sourceLanguage' => 'en',
                    'fileMap'        => ['app' => 'app.php'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable'       => '{{%auth_item}}',
            'itemChildTable'  => '{{%auth_item_child}}',
            'assignmentTable' => '{{%auth_assignment}}',
            'ruleTable'       => '{{%auth_rule}}',
        ],
        'session' => [
            'class'          => 'yii\web\Session',
            'cookieParams'   => [
                'httpOnly' => true,
                'lifetime' => 3600 * 24,
            ],
            'timeout'        => 3600 * 24,
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'hello-world' => 'hello-world/index',
                'map'         => 'map/index',
                'map/<action:\w[\w-]*>' => 'map/<action>',
                'map/<action:\w[\w-]*>/<id:\d+>' => 'map/<action>',
                'auth/register' => 'auth/register',
                'auth/register-done' => 'auth/register-done',
                'auth/login' => 'auth/login',
                'auth/verify' => 'auth/verify',
                'auth/logout' => 'auth/logout',
                'telegram/webhook' => 'telegram/webhook',
                'telegram/set-webhook' => 'telegram/set-webhook',
                'language/<lang:ru|en>' => 'site/language',
                '<controller:(fuel-station|authority|medical-point|marina|service-station|emergency)>' => '<controller>/index',
                '<controller:(fuel-station|authority|medical-point|marina|service-station|emergency)>/<action:\w[\w-]*>' => '<controller>/<action>',
                '<controller:(fuel-station|authority|medical-point|marina|service-station|emergency)>/<action:\w[\w-]*>/<id:\d+>' => '<controller>/<action>',
                'menu' => 'menu/index',
                'menu/<action:\w[\w-]*>' => 'menu/<action>',
                'menu/<action:\w[\w-]*>/<id:\d+>' => 'menu/<action>',
                'user' => 'user/index',
                'user/<action:\w[\w-]*>' => 'user/<action>',
                'user/<action:\w[\w-]*>/<id:\d+>' => 'user/<action>',
            ],
        ],
    ],
    'params' => $params,
    'on beforeRequest' => function () {
        $lang = Yii::$app->session->get('language', 'ru');
        if (in_array($lang, ['ru', 'en'], true)) {
            Yii::$app->language = $lang;
        }
    },
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
