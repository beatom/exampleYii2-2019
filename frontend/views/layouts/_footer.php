<?php
use common\models\Options;
use yii\helpers\Url;

//$social = Options::getSocialLink();

$menu = '';
$menuItems = [
    ['label' => Yii::t('app', 'О компании'), 'url' => '/about'],
    ['label' => Yii::t('app', 'Команда'), 'url' => '/about#team'],
    ['label' => Yii::t('app', 'Документация'), 'url' => '/about#agreements'],
    ['label' => Yii::t('app', 'Доходность'), 'url' => '/profitability'],
    ['label' => Yii::t('app', 'Преимущества'), 'url' => '/about#benefits'],
    ['label' => Yii::t('app', 'Почему мы?'), 'url' => '/index#why_us'],
    ['label' => Yii::t('app', 'Контакты'), 'url' => '/contact'],
    ['label' => Yii::t('app', 'ИнфоБаза'), 'url' => '/regulations'],
    ['label' => Yii::t('app', 'Новости'), 'url' => '/news/index'],
];

foreach ($menuItems as $item) {
    $menu .= '<li ><a href="' . Url::to([$item['url']]) . '">' . $item['label'] . '</a></li>';
}
?>

<!--<a href="--><?//= Url::to(['/news/index']) ?><!--" class="news">-->
<!---->
<!--        <div id="new_news_notification" class="news__items" style="display:none;">-->
<!--            <div class="news__count">0</div>-->
<!--            <div class="news__live">live</div>-->
<!--        </div>-->
<!--    <div class="news__title">news</div>-->
<!--</a>-->
<?php
if (!isset($_COOKIE['invest_video_presentation'])) { ?>
<div id="invest_video_presentation" class="show-video" style="background-image: url(/img/обложка.png)">
    <div class="show-video__close">
        <svg width="14px" height="14px">
            <path fill-rule="evenodd" fill="rgb(49, 49, 49)" d="M13.660,13.216 L13.216,13.660 L7.000,7.444 L0.784,13.660 L0.340,13.216 L6.556,7.000 L0.340,0.783 L0.784,0.339 L7.000,6.556 L13.216,0.339 L13.660,0.783 L7.444,7.000 L13.660,13.216 Z"></path>
        </svg>
    </div>
    <div class="show-video__title" data-toggle="modal" data-target="#show-video" data-video="https://www.youtube.com/embed/D2pDzJK3_84">Что такое invest?</div>
    <div class="u-play-style" data-toggle="modal" data-target="#show-video" data-video="https://www.youtube.com/embed/D2pDzJK3_84">
        <div class="u-play-video-btn">
            <div class="u-play-video-triangle"></div>
            <div class="u-play-video-circle-rotate" name="play"></div>
        </div>
    </div>
    <div class="show-video__already-seen">Спасибо уже видели</div>
</div>
<?php } ?>
<?= $this->render('_video_popup')?>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col">
              <div class="footer-logo"><img class="logo-img" src="/img/svg/logo-img.svg" alt="">
                <svg class="logo-text">
                  <use xlink:href="./img/sprites/sprite.svg#logo-text"></use>
                </svg>
              </div>
                <ul class="footer__contact-info">
                    <li><span class="phone"></span>8 800 511 85 03</li>
                    <li><a href="mailto:support@invest.biz"><span class="mail"></span>support@invest.biz</a></li>
                </ul>
                <div class="copy">&copy; 2019 invest.</div>
                <a href="//www.free-kassa.ru/"><img src="//www.free-kassa.ru/img/fk_btn/17.png" title="Прием платежей на сайте"></a>
            </div>
            <div class="col d-flex align-items-end">
                <ul class="footer__menu">
                  <?= $menu ?>
                </ul>
            </div>
        </div>
    </div>
</footer>
