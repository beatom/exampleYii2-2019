<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
use common\models\Options;

\frontend\assets\LoginAsset::register($this);
Yii::$app->i18nJs;
$social = Options::getSocialLink();

$user = Yii::$app->user->identity;

$menu = '';
$menuItems = [
    ['label' => Yii::t('app', 'Главная'), 'url' => '/'],
    ['label' => Yii::t('app', 'Доходность'), 'url' => '/site/profitability'],
    ['label' => Yii::t('app', 'О компании'), 'url' => '/site/about'],
    ['label' => Yii::t('app', 'ИнфоБаза'), 'url' => '/site/regulations'],
    ['label' => Yii::t('app', 'Контакты'), 'url' => '/site/contact'],
];

foreach ($menuItems as $item) {
    if ($item['url'] == '/' . $this->context->module->requestedRoute) {
        $menu .= '<li class="nav-item active"><a class="nav-link" href="' . Url::to([$item['url']]) . '">' . $item['label'] . '</a></li>';
    } else {
        $menu .= '<li class="nav-item"><a class="nav-link" href="' . Url::to([$item['url']]) . '">' . $item['label'] . '</a></li>';
    }
}
$social = Options::getSocialLink();
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

<body>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '672387683183022',
            cookie     : true,
            xfbml      : true,
            version    : 'v3.2',
            scope: 'publish_actions,email'
        });

        FB.AppEvents.logPageView();
        FB.getLoginStatus(function(response) {
            console.log(response);
        });

    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));


    /*---------------------------------------------*/
    /* FB вход */
    // Only works after `FB.init` is called
    function myFacebookLogin() {
        FB.login(function(response){
            if(response.status == 'connected') {
                FB.api('/me', {fields: 'id,name,email'}, function (response) {
                    var fb_id = response.id;
                    var csrfToken = $('meta[name="csrf-token"]').attr("content");

                    $.ajax({
                        url: '/site/ajax',
                        data: {
                            email: response.email,
                            id:    response.id,
                            name:  response.name,
                            action: 'fb_login',
                            _csrf: csrfToken
                        },
                        type: 'POST',
                        success: function (data) {
                            data = JSON.parse(data);

                            if(data.success == true) {
                                console.log('relode');
                                window.location.href = '/user/index';
                            }
                            else{
                                if(data.url){
                                    window.location.href = '/user/index';
                                }
                                else{
                                    alert(data.message);
                                }
                            }
                        }
                    });
                });
            }

        }, {scope: 'email'});
    }
    function logoutFB() {
        FB.logout(function (response) {
            console.log('logout');
            console.log(response);
        });
    }
</script>
<?php $this->beginBody() ?>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->


<nav class="navbar navbar-expand-lg registration-header fixed-top white__header">
    <div class="container"><a class="navbar-brand" href="<?= Url::to(['/']) ?>"><div class="logo"><img class="logo-img" src="/img/svg/logo-img.svg" alt=""><img class="logo-text" src="/img/svg/logo-text.svg" alt=""></div></a>
        <div class="navbar-toggler collapsed" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse"><span></span><span></span><span></span><span></span></div>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav">
                <?= $menu ?>
            </ul>
            <div class="header-top__items">
                <ul class="header-top__social">
                    <li><a href="<?= $social['vk'] ?>"><span class="vk"></span></a></li>
                    <li><a href="<?= $social['telegram'] ?>"><span class="telegram"></span></a></li>
                    <li><a href="<?= $social['youtube'] ?>"><span class="youtube"></span></a></li>
                </ul>
            </div>
            <a href="<?= Url::to(['/user/index']) ?>" class="header-top__personal ml-auto">Личный кабинет</a>
        </div>
    </div>
</nav>
<?= Alert::widget() ?>


<?= $content ?>

<?php $this->endBody() ?>

<?= Options::getOptionValueByKey('jivosite_code') . "\n" ?>
<?= Options::getOptionValueByKey('yandex_metrica') . "\n" ?>
</body>
</html>
<?php $this->endPage() ?>
