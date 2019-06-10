<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use common\widgets\ChatWidget;
use yii\helpers\Url;
use common\models\Options;

\frontend\assets\CabinetAsset::register($this);
Yii::$app->i18nJs;
$social = Options::getSocialLink();

$user = Yii::$app->user->identity;
$before_ico = '';
$h = date('H');
if ($h >= 15 OR $h < 10) {
    $before_ico_text = '';
    if(Yii::$app->user->identity->events_complete) {
        $before_ico_text = 'Новый результат';
    } elseif(Yii::$app->user->identity->events_notice) {
        $before_ico_text = 'Новое событие!';
    }
    if($before_ico_text != '') {
        $before_ico = '<div class="new_bets notification-item">'.$before_ico_text.'<div class="notification"><span></span></div></div>';
    } else {
        $before_ico =  '<div class="new_bets notification-item" style="display: none;">Новый результат<div class="notification"><span></span></div></div>';
    }
}


$menu = '';
$menuItems = [
    ['label' => Yii::t('app', 'Мой кабинет'), 'url' => '/user', 'ico' => 'cabinet-ico'],
    ['label' => Yii::t('app', 'Пополнить / Снять'), 'url' => '/user/deposit', 'ico' => 'deposit-ico'],
    ['label' => Yii::t('app', 'Ставки invest'), 'url' => '/user/bets', 'ico' => 'rates-ico', 'before_ico' => $before_ico],
    ['label' => Yii::t('app', 'Партнерский Кэшбэк'), 'url' => '/user/cashback', 'ico' => 'cashback-ico'],
    ['label' => Yii::t('app', 'Промо- материалы'), 'url' => '/user/promo', 'ico' => 'promo-ico'],
    ['label' => Yii::t('app', 'История операций'), 'url' => '/user/history', 'ico' => 'history-ico'],
    ['label' => Yii::t('app', 'Новости'), 'url' => '/news/index', 'ico' => 'news-ico'],
];

foreach ($menuItems as $item) {
    $add = isset($item['before_ico']) ? $item['before_ico'] : null;
    if ($item['url'] == '/' . $this->context->module->requestedRoute) {
        $menu .= '<li class="active"><a href="' . Url::to([$item['url']]) . '">' . $add . '<div class="icon ' . $item['ico'] . '"></div>' . $item['label'] . '</a></li>';
    } else {

        $menu .= '<li><a href="' . Url::to([$item['url']]) . '">' . $add . '<div class="icon ' . $item['ico'] . '"></div>' . $item['label'] . '</a></li>';
    }
}
$social = Options::getSocialLink();
$unread_messages = $user->countUnreadMessages();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <meta name="theme-color" content="#161719">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
</head>

<body <?= $this->context->module->requestedRoute == 'user/index' ? 'style="overflow: visible;"': null ?>>
<?php $this->beginBody() ?>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->

<div class="container-fluid">
    <div class="row">
        <nav class="left-side navbar navbar-expand-lg col flex-column">
            <a class="logo logo__big" href="<?= Url::to(['/']) ?>">
                <img src="/img/svg/black-logo.svg">
            </a>
            <div class="navbar-toggler collapsed" data-toggle="collapse" data-target="#menu" aria-controls="menu"><span></span><span></span><span></span><span></span></div>
            <a class="logo logo__small" href="<?= Url::to(['/']) ?>">
                <img src="/img/svg/black-logo.svg">
            </a>
            <div class="collapse navbar-collapse" id="menu">
                <ul class="menu-list">
                    <?= $menu ?>
                </ul>
            </div>
        </nav>
        <main class="main-items">
            <div class="personal-header">
                <ul class="personal-menu mr-md-auto d-none d-lg-flex">
                    <li><a href="<?= Url::to(['/site/profitability']) ?>">Доходность</a></li>
                    <li><a href="<?= Url::to(['/site/about']) ?>">О компании</a></li>
                    <li><a href="<?= Url::to(['/site/regulations']) ?>">ИнфоБаза</a></li>
                    <li><a href="<?= Url::to(['/site/contact']) ?>">Контакты</a></li>
                </ul>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <img class="personal-avatar" src="<?= $user->avatar ?>">
                        <?= $user->getNameString() ?>
                      <div class="new_message_notification <?= $unread_messages ? 'show_notification' : null ?>"><img src="/img/message-notification.png" /><span><?= $unread_messages?></span></div>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="<?= Url::to(['/user/settings']) ?>">
                            <svg class="icon">
                                <use xlink:href="/img/sprites/sprite.svg#settings"></use>
                            </svg>
                            Управление аккаунтом
                        </a>
                        <a class="dropdown-item" href="<?= Url::to(['/user/messages']) ?>">
                            <svg class="icon">
                                <use xlink:href="/img/sprites/sprite.svg#mail"></use>
                            </svg>
                            Сообщения
                              <span class="new_message_notification <?= $unread_messages ? 'show_notification' : null ?>"><?= $unread_messages?></span>
                        </a>
                        <a class="dropdown-item" href="<?= Url::to(['/logout']) ?>">
                            <svg class="icon">
                                <use xlink:href="/img/sprites/sprite.svg#out"></use>
                            </svg>
                            Выход
                        </a>
                    </div>
                </div>
            </div>
            <?= $content ?>
        </main>
    </div>
</div>
<?= ChatWidget::widget() ?>
<?php
if (!$user->first_banner_shown AND $this->context->module->requestedRoute !== 'user/deposit') { ?>
    <div class="modal-steps-background" style="display: none;">
        <div class="modal-steps">
            <button class="close" type="button" data-dismiss="modal" aria-label="Close" style="/*! margin-right: 27px; */position: absolute;right: 15px;top: 15px;"><svg width="14px" height="14px"><path fill-rule="evenodd" fill="rgb(49, 49, 49)" d="M13.660,13.216 L13.216,13.660 L7.000,7.444 L0.784,13.660 L0.340,13.216 L6.556,7.000 L0.340,0.783 L0.784,0.339 L7.000,6.556 L13.216,0.339 L13.660,0.783 L7.444,7.000 L13.660,13.216 Z"></path></svg></button>
            <div class="modal-steps--title">Осталось всего 2 шага!</div>
            <div class="modal-steps--levels">
                <div class="modal-steps--levels-n ready">1</div>
                <div class="modal-steps--levels-line ready"></div>
                <div class="modal-steps--levels-n">2</div>
                <div class="modal-steps--levels-line"></div>
                <div class="modal-steps--levels-n">3</div>
            </div>
            <div class="modal-steps--flex">
                <div class="modal-steps--item ready">
                    <div class="modal-steps--item-icon">
                        <img src="/img/website.png" alt="">
                        <span><img src="/img/website-burgundy.png" alt=""></span>
                    </div>
                    <div class="modal-steps--item-text">Регистрация</div>
                </div>
                <div class="modal-steps--item">
                    <div class="modal-steps--item-icon">
                        <img src="/img/wallet.png" alt="">
                        <span><img src="/img/wallet-burgundy.png" alt=""></span>
                    </div>
                    <div class="modal-steps--item-text">Пополни банк от 5$</div>
                </div>
                <div class="modal-steps--item">
                    <div class="modal-steps--item-icon">
                        <img src="/img/money.png" alt="">
                        <span><img src="img/money-burgundy.png" alt=""></span>
                    </div>
                    <div class="modal-steps--item-text">Получай профит</div>
                </div>
            </div>
            <a href="<?= Url::to('/user/deposit') ?>" class="modal-steps--btn default-btn">Пополнить</a>
        </div>
    </div>
<?php }
$this->endBody() ?>

<?= Options::getOptionValueByKey('jivosite_code') . "\n" ?>
<?= Options::getOptionValueByKey('yandex_metrica') . "\n" ?>
<script src='lib/bodyScrollLock.js'></script>
</body>
</html>
<?php $this->endPage() ?>
