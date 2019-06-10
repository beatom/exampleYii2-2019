<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
use common\models\Options;
use common\widgets\ChatWidget;

AppAsset::register($this);
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
    <meta name="yandex-verification" content="f6a48972a846e3c3">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/favicon2.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon2.ico">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon2.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon2.ico">
</head>

<body>
<?php $this->beginBody() ?>
<!--[if lte IE 9]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
<![endif]-->

<nav class="navbar navbar-expand-lg registration-header fixed-top <?= $this->context->module->requestedRoute != 'site/index' ? 'white__header' : 'nobg_header' ?>">
    <div class="container"><a class="navbar-brand" href="<?= Url::to(['/']) ?>">
            <div class="logo"><img class="logo-img" src="/img/svg/logo-img.svg" alt=""><div class="logo-text"></div></div>
        </a>
        <div class="navbar-toggler collapsed" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse"><span></span><span></span><span></span><span></span></div>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="navbar-nav">
                <?= $menu ?>
            </ul>
            <?php if(Yii::$app->user->isGuest) { ?>
                <a href="<?= Url::to(['/user/index']) ?>" class="header-top__personal ml-md-auto">Личный кабинет</a>
            <?php } else { ?>
                <a class="header-top__personal header-top__avatar ml-auto" href="<?= Url::to(['/user/index']) ?>"><img src="<?= Yii::$app->user->identity->avatar ?>" alt=""><?= Yii::$app->user->identity->getNameString() ?></a>
            <?php } ?>


        </div>
    </div>
</nav>
<?= Alert::widget() ?>

<div class="outer-indent">
    <?= $content ?>
</div>
<?= ChatWidget::widget() ?>
<?php $this->endBody() ?>
<?php
$this->beginContent('@app/views/layouts/_footer.php');
$this->endContent();

$current_time = time();
?>
<script id="vertexshader" type="x-shader/x-vertex">
      attribute float scale;
      void main() {
      vec4 mvPosition = modelViewMatrix * vec4( position, 1.0 );
      gl_PointSize = scale * ( 300.0 / - mvPosition.z );
      gl_Position = projectionMatrix * mvPosition;
      }


</script>
<script id="fragmentshader" type="x-shader/x-fragment">
      uniform vec3 color;
      void main() {
      if ( length( gl_PointCoord - vec2( 0.5, 0.5 ) ) > 0.475 ) discard;
      gl_FragColor = vec4( color, 1.0 );
      }



</script>
<?= Options::getOptionValueByKey('jivosite_code') . "\n" ?>
<?= Options::getOptionValueByKey('yandex_metrica') . "\n" ?>
</body>
</html>
<?php $this->endPage() ?>
