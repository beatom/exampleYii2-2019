<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'authManager' => [
            'class'           => 'yii\rbac\DbManager',
            'itemTable'       => 'auth_item',
            'itemChildTable'  => 'auth_item_child',
            'assignmentTable' => 'auth_assignment',
            'ruleTable'       => 'auth_rule',
            'defaultRoles'    => ['guest'],// роль которая назначается всем пользователям по умолчанию
        ],
        'mutex' => [
            'class' => 'yii\mutex\MysqlMutex'
        ],
        'user' => [
	        'class' => 'yii\web\User',
	        'identityClass' => 'common\modules\User',
	        'loginUrl' => ['/users/default/login'],
//	        'on afterLogin' => function($event)
//	        {
//		        Yii::$app->user->identity->afterLogin($event);
//	        }
        ],
    ],
    'language' => 'ru',
    'sourceLanguage' => 'ru',
    'timeZone' => 'Europe/Moscow',
];
