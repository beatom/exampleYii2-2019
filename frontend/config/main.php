<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'sourceLanguage' => 'ru',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'languages'
    ],
    'controllerNamespace' => 'frontend\controllers',
    'controllerMap' => [
        'images' => [
            'class'         => 'phpnt\cropper\controllers\ImagesController',
        ],
    ],
    'modules' => [
        'languages' => [
            'class' => 'common\modules\languages\Module',
            //Языки используемые в приложении
            'languages' => [
                'en' => 'en',
                'ru' => 'ru',
            ],
            'default_language' => 'ru', //основной язык (по-умолчанию)
            'show_default' => false, //true - показывать в URL основной язык, false - нет
        ],
    ],
    'components' => [
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'forceTranslation' => true,
                    'basePath' => '@common/messages',
                ],
                'cab' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    //'forceTranslation' => true,
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'i18nJs' => [
            'class' => 'w3lifer\yii2\I18nJs',
        ],
        'request' => [
            'baseUrl' => '', // убрать frontend/web
            'class' => 'common\components\Request',
            'csrfParam' => '_csrf-frontend',
            'cookieValidationKey' => $params['cookieValidationKey'],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true, 'domain' => $params['cookieDomain']],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
            'cookieParams' =>[
                'httpOnly' => true,
                'domain' => $params['cookieDomain'],
            ]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [

                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'categories' => [
                        'yii\db\*',
                    ],
                    'logFile' => '@app/runtime/logs/db_error.log',
                ],

                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logFile' => '@app/runtime/logs/error.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning', 'error', 'info'],
                    'exportInterval' => 1,
                    'logVars' => [/*'_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'*/],
                    'categories' => ['terminal'],
                    'logFile' => '@app/runtime/logs/terminal.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error'],
                    'exportInterval' => 1,
                    'logVars' => [/*'_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'*/],
                    'categories' => ['terminal_error'],
                    'logFile' => '@app/runtime/logs/terminal_error.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning', 'error', 'info'],
                    'exportInterval' => 1,
                    'logVars' => [/*'_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'*/],
                    'categories' => ['api'],
                    'logFile' => '@app/runtime/logs/api.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['warning', 'error', 'info'],
                    'exportInterval' => 1,
                    'logVars' => [/*'_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'*/],
                    'categories' => ['sendpulse'],
                    'logFile' => '@app/runtime/logs/sendpulse.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'class' => 'common\components\UrlManager',
            'languages' => 'languages/default/index', //для модуля мультиязычности
            'rules' => [
                '/' => 'site/index',
                //'regulations' => 'site/rules',


                'user' => 'user/index',
                'user/bets/<date:[-\w]+>' => 'user/bets',
                'user/bets' => 'user/bets',
                '/<invite:\w{10}>' => 'site/index',
                '<action:\w+>' => 'site/<action>',


                '<id:[-\d]+>-<synonym:[-\w]+>' => 'site/page',
                'news/<id:[-\d]+>-<synonym:[-\w]+>' => 'news/single',
                'shares/<id:[-\d]+>-<synonym:[-\w]+>' => 'shares/single',

                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',

            ],
        ],

    ],
    'params' => $params,
];
