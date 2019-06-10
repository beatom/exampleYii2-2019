<?php

use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;


NavBar::begin([
    'options' => [
        'class' => '',
    ],
]);
$menuItems=[];
//$menuItems[] = [
//    'label' => 'Информационные страницы',
//    'items' => [
//        ['label' => 'Главная страница', 'url' => ['/seting/home-page']],
//        ['label' => 'Страница "Торговать"', 'url' => ['/seting/trade']],
//        ['label' => 'Страница "Партнёрская программа"', 'url' => ['/seting/partnership']],
//        ['label' => 'Страница "О компании"', 'url' => ['/seting/about']],
//    ]
//];

$menuItems[] = ['label' => 'Смс провайдеры', 'url' => ['site/sms_settings'],
];

$menuItems[] = ['label' => 'Аккаунты рассылок', 'url' => ['/messages/accounts'],
];


$menuItems[] = ['label' => 'Цели', 'url' => ['/seting/objectives'],
];

$menuItems[] = ['label' => 'Платежные системы', 'url' => ['/seting/payments'],
];

$menuItems[] = ['label' => 'Соц сети', 'url' => ['/seting/social']];
$menuItems[] = ['label' => 'Менеджеры', 'url' => ['/seting/manager-cards']];

//$menuItems[] = ['label' => 'Тестирование', 'url' => ['/seting/testing'],
//];


echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar'],
    'items' => $menuItems,
]);

NavBar::end();