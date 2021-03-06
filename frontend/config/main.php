<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-Ru',
    'timeZone' =>'Europe/Moscow',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/index',
    'on beforeAction' => function ($event) {
        if (!Yii::$app->user->isGuest) {
            $user = \frontend\models\User::findOne(Yii::$app->user->id);
            $user->last_visit_date = null;
            $user->save();
        }
    },
    'components' => [
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'encryption' => 'tls',
                'host' => 'smtp.mailtrap.io',
                'port' => '2525',
                'username' => '9440786d7c0c96',
                'password' => 'ff990d4b699398',
            ]
        ],
        'cache' => [
            'class' => 'yii\redis\Cache',
            'redis' => [
                'class' =>'yii\redis\Connection',
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 0,
            ]
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '8084301',
                    'clientSecret' => 'UQyNgaTSrEKCQY52pe4w',
                    'scope' => 'email',
                ],
            ],
        ],
        'request' => [
            'parsers' => [
                'application/json' =>'yii\web\JsonParser',
            ],
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'frontend\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
            'loginUrl' => 'site/index',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'tasks' => 'tasks/index',
                'users' => 'users/index',
                'mylist' => 'my-list/index',
                'favorite/<chosenUserId:\d+>' =>'users/switch-favorite',
                'task/view/<id:\d+>' => 'tasks/view',
                'task/create' => 'tasks/create',
                'task/confirm/<taskId:\d+>/<respondId:\d+>' => 'tasks/confirm',
                'task/deny/<taskId:\d+>/<respondId:\d+>' => 'tasks/deny',
                'user/view/<id:\d+>' => 'users/view',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/messages'],
            ],
        ],
    ],
    'params' => $params,
    'modules' => [
        'api' => [
            'class' => 'frontend\modules\api\Module'
        ]
    ],
];
