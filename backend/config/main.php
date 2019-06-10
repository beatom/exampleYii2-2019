<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'name'=>'invest',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'controllerMap' => [
        'images' => [
            'class'         => 'phpnt\cropper\controllers\ImagesController',
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
//            'enableStrictParsing' => true,
            'rules' => [
                '/' => 'site/index',
                'users/default/login' => 'site/login',
                'messages' => 'messages/index',
                'messages/<id:\d+>' => 'messages/chats',
                'messages/<senderId:\d+>/<id:\d+>' => 'messages/chat',
                'messages/make-chat/<senderId:\d+>/<id:\d+>' => 'messages/make-chat',
                'messages/edit-account/<id:\d+>' => 'messages/edit-account',
                'messages/edit-template/<id:\d+>' => 'messages/edit-template',

                'user/<id:\d+>' => 'user/user',
                'user/edit/<id:\d+>' => 'user/edit',
                'user/<id:\d+>/overdraft' => 'user/overdraft',
                'user/<id:\d+>/balance' => 'user/balance',
                'user/<id:\d+>/partnerka' => 'user/partnerka',
                'user/<id:\d+>/balance-bonus' => 'user/balance-bonus',
                'user/remove-bonus-debt/<id:\d+>' => 'user/remove-bonus-debt',

                'site/seven-bonus-decline/<id:\d+>' => 'site/seven-bonus-decline',
                'site/seven-bonus-approve/<id:\d+>' => 'site/seven-bonus-approve',

                'events/list/<id:\d+>' => 'events/list',
                'events/list' => 'events/list',

                'chat/add/<parent_id:\d+>' => 'chat/add',
                'chat/add' => 'chat/add',

                'user/account-reviews/<id:\d+>' => 'user/account-reviews',
                'user/add-account-review/<id:\d+>' => 'user/add-account-review',
                'user/edit-account-review/<id:\d+>' => 'user/edit-account-review',
                'user/delete-account-review/<id:\d+>' => 'user/delete-account-review',

                'user/solution-reviews/<id:\d+>' => 'user/solution-reviews',
                'user/add-solution-review/<id:\d+>' => 'user/add-solution-review',
                'user/edit-solution-review/<id:\d+>' => 'user/edit-solution-review',
                'user/delete-solution-review/<id:\d+>' => 'user/delete-solution-review',
                'user/objectives/<id:\d+>' => 'user/objectives',

                'user/card-payment-log' => 'user/card-payment-log',
                'user/overdraft-log' => 'user/overdraft-log',

                'trade/edit-account/<id:\d+>' => 'trade/edit-account',
                'trade/profit-by-day/<id:\d+>' => 'trade/profit-by-day',
                'trade/edit-profit-by-day/<id:\d+>' => 'trade/edit-profit-by-day',
                'trade/make-trade-offer/<id:\d+>' => 'trade/make-trade-offer',
                'trade/trade-offer/<id:\d+>' => 'trade/trade-offer',
                'trade/delete-account/<id:\d+>' => 'trade/delete-account',
                'trade/change-password/<id:\d+>' => 'trade/change-password',
                'trade/invest-account/<id:\d+>' => 'trade/invest-account',
                'trade/leverage-log' => 'trade/leverage-log',
                'trade/leverage-approve/<id:\d+>' => 'trade/leverage-approve',
                'trade/leverage-decline/<id:\d+>' => 'trade/leverage-decline',
                
                'trade/solution-start/<id:\d+>' => 'trade/solution-start',
                'trade/solution-reviews/<id:\d+>' => 'trade/solution-reviews',
                'trade/solution-edit-review/<id:\d+>' => 'trade/solution-edit-review',
                'trade/solution-add-review/<id:\d+>' => 'trade/solution-add-review',
                'trade/solution-delete-review/<id:\d+>' => 'trade/solution-delete-review',
                
                'trade/close-investment/<id:\d+>' => 'trade/close-investment',
                'trade/withdraw_investment/<id:\d+>' => 'trade/withdraw_investment',

                'trade/account-actions/<id:\d+>' => 'trade/account-actions',
                'trade/account-periods/<id:\d+>' => 'trade/account-periods',
                'trade/account-period/<id:\d+>' => 'trade/account-period',
                
                'trade/make-new-account/<id:\d+>' => 'trade/make-new-account',
                'trade/rollover-plan' => 'trade/rollover-plan',

                'trade/account-investments/<id:\d+>' => 'trade/account-investments',
                'trade/solution-investments/<id:\d+>' => 'trade/solution-investments',
                'trade/check-deals/<id:\d+>' => 'trade/check-deals',
                'trade/account-debts/<id:\d+>' => 'trade/account-debts',
                'trade/account-debt/<id:\d+>' => 'trade/account-debt',

                'trade/block-account/<id:\d+>' => 'trade/block-account',
                'trade/bonus-list/<id:\d+>' => 'trade/bonus-list',
                'trade/akstia-list/<id:\d+>' => 'trade/akstia-list',


                'trade/bonus-end' => 'trade/bonus-end',
                
                'trade/investment-by-day/<id:\d+>' => 'trade/investment-by-day',
                'trade/investment-actions/<id:\d+>' => 'trade/investment-actions',
                'trade/investment-protection/<id:\d+>' => 'trade/investment-protection',

                'reviews/chat-edit/<id:\d+>' => 'reviews/chat-edit',
                
                'trade/withdraw-investments' => 'trade/withdraw-investments',
                'trade/withdraw-decline/<id:\d+>' => 'trade/withdraw-decline',

                'trade/add-review/<id:\d+>' => 'trade/add-review',
                'trade/edit-review/<id:\d+>' => 'trade/edit-review',
                'trade/delete-review/<id:\d+>' => 'trade/delete-review',

                'trade/delete-accounts-log' => 'trade/delete-accounts-log',
                'trade/delete-accounts-log-approve/<id:\d+>' => 'trade/delete-accounts-log-approve',

                'trade/sort-accounts' => 'trade/sort-accounts',
                'trade/sort-accounts-profit' => 'trade/sort-accounts-profit',
                'trade/sort-accounts-positions' => 'trade/sort-accounts-positions',
                'trade/account-change-history/<id:\d+>' => 'trade/account-change-history',
                'trade/solution-bonuses' => 'trade/solution-bonuses',
                'trade/solution-bonus/<id:\d+>' => 'trade/solution-bonus',
                'trade/solution-bonus-add' => 'trade/solution-bonus-add',
                'trade/solution-bonus-delete/<id:\d+>' => 'trade/solution-bonus-delete',
                'trade/remove-from-solution/<solution_id:\d+>/<account_id:\d+>' => 'trade/remove-from-solution',

                'trade/create-solution' => 'trade/create-solution',
                'trade/edit-solution/<id:\d+>' => 'trade/edit-solution',
                'trade/solution-composition/<id:\d+>' => 'trade/solution-composition',
                'trade/find-accounts-solution' => 'trade/find-accounts-solution',

                'trade/find-accounts-all' => 'trade/find-accounts-all',

                'site/bonus-add/<id:\d+>' => 'site/bonus-add',
                'site/bonus-invest/<id:\d+>' => 'site/bonus-invest',

                'seting/manager-card/<id:\d+>' => 'seting/manager-card',
                'seting/delete-manager-card/<id:\d+>' => 'seting/delete-manager-card',


                '<action:\w+>' => 'site/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
            ],
        ],
        
    ],
    'params' => $params,
];
