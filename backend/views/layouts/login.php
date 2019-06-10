<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use common\models\Options;
use common\models\UserDoc;

\backend\assets\LoginAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        // 'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);


    $menuItems = [];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Войти', 'url' => ['/site/login']];
    } else {

        $menuItems[] = ['label' => 'Страницы',
            'items' => [
                ['label' => 'Новости', 'url' => ['/news/index']],
                ['label' => 'Акции', 'url' => ['/shares/index']],
                ['label' => 'Статические страницы', 'url' => ['/page/index']],
            ]
        ];

        $newDocuments = UserDoc::countNewDocuments();
        $documentsBadge = ($newDocuments == 0) ? '' : $newDocuments;
        $menuItems[] = ['label' => 'Пользователи '. Html::tag('span', $documentsBadge, ['class' => 'badge']),
            'items' => [
                ['label' => 'Все пользователи', 'url' => ['/user/index']],
                ['label' => 'Документы '. Html::tag('span', $documentsBadge, ['class' => 'unread badge']) ,'url' => ['/user/documents']],
                ['label' => 'Баланс пользователей', 'url' => ['/site/balance-log']],
                ['label' => 'Отзывы об управляющих', 'url' => ['/reviews/index']],
                ['label' => 'Отзыв-чат', 'url' => ['/reviews/chat']],
            ]
        ];

        $menuItems[] = ['label' => 'Финанансы ',
            'items' => [
                ['label' => 'Торговые счета', 'url' => ['/trade/accounts']],
                ['label' => 'Готовое решение', 'url' => ['/trade/solutions']],
            ]
        ];

        $menuItems[] = ['label' => 'Баннера',
            'items' => [
                ['label' => 'Слайды на главной', 'url' => ['/banner/main']],
                ['label' => 'Слайды в кабинете', 'url' => ['/banner/cabinet-slides']],
                ['label' => 'Баннера в кабинете', 'url' => ['/banner/cabinet-banners']],
            ]
        ];
        $menuItems[] = ['label' => 'Настройки', 'url' => ['/seting/index']];

        $menuItems[] = ['label' => 'Шаблоны',
            'items' => [
                ['label' => 'SMS', 'url' => ['/site/sms_template']],
                ['label' => 'E-mail', 'url' => ['site/email_template']],
                ['label' => 'Сообщения', 'url' => ['/messages/templates']],
            ]
        ];
        $messagesBadge = (Yii::$app->user->identity->unread_messages == 0) ? '' : Yii::$app->user->identity->unread_messages;
        $menuItems[] = ['label' => 'Сообщения '. Html::tag('span', $messagesBadge, ['class' => 'unread badge']), 'options' => ['class' => 'messages-menu'],
            'items' => [
                ['label' => 'Общение '. Html::tag('span', $messagesBadge, ['class' => 'unread badge']) , 'url' => ['/messages']],
                ['label' => 'Массовая рассылка', 'url' => ['/messages/mass-messages']],
                ['label' => 'Лог рассылок', 'url' => ['/messages/history']],
            ]
        ];


        $menuItems[] = ['label' => 'Логи',
            'items' => [
                ['label' => 'Cмс', 'url' => ['site/sms_log']],
                ['label' => 'Движение средств', 'url' => ['site/money_log']],
                ['label' => 'Бонусов', 'url' => ['site/bonus-log']],
                ['label' => 'Балов', 'url' => ['site/ball-log']],
                ['label' => 'Партнерский счет', 'url' => ['site/partner-log']],
                ['label' => 'Изменение кредитного плеча', 'url' => ['trade/leverage-log']],
                ['label' => 'Вывод инвестиций', 'url' => ['trade/withdraw-investments']],
                ['label' => 'Удаление счетов', 'url' => ['trade/delete-accounts-log']],
                ['label' => 'Защита капитала', 'url' => ['trade/protectionlog']],
                ['label' => 'Переводы на карту', 'url' => ['user/card-payment-log']],
                ['label' => 'Овердрафт', 'url' => ['user/overdraft-log']],
            ]
        ];

//        $menuItems[] = ['label' => 'Отзывы',
//            'items' => [
//                ['label' => 'Установка данных', 'url' => ['site/test']],
//                ['label' => 'Тест ДУ', 'url' => ['trade/testdu']],
//            ]
//        ];


        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Выйти (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
        'encodeLabels' => false,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
