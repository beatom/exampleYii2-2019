<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Торговать');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="c-trade-top trade-items">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <h1 class="wow fadeInLeft">Получайте доход от  трейдинга на рынке Форекс!</h1>
                <p class="wow fadeInLeft">Зарабатывайте на изменении котировок Мировых компаний, драгоценных металлов и валютных пар!</p>
                <a href="<?= Url::to(['/user/trade']) ?>" title="" class="wow fadeInLeft c-btn u-btn-benefit-start-now" data-wow-delay="0.5s">
                    <span>Начать прямо сейчас</span>
                </a>
            </div>
            <div class="col-md-7">
                <img src="../img/trade-graf.png" alt="" class="wow fadeInRight img-responsive" style="max-width: 679px;margin-top: -25px;">
            </div>
        </div>
    </div>
</div>
<div class="training-center">
    <div class="container">
        <div class="row justify-content-end">
            <div class="col-lg-5 col-md-7">
                <h1 class="wow fadeInRight training-center--title">Учебный центр invest24</h1>
                <p class="wow fadeInRight">Самый доступный курс по трейдингу для новичков из 10 простых занятий!</p>
                <div class="wow fadeInRight training-center--info">
                    <div class="training-center-ico"></div>
                    <div class="training-center--info__description">
                        По окончанию курса
                        <span class="price-trade">100$ <span>на депозит <br>для торговли!</span></span>
                    </div>
                </div>
                <a href="https://training.invest24.com" title="" class="wow fadeInRight c-btn" data-wow-delay="0.5s">
                    <span>НАЧАТЬ ОБУЧЕНИЕ</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!--   блок ТОП -->
<div class="c-trade-top">
    <div class="container">
        <h1 class="wow fadeIn" data-wow-delay="0.5s"><?= Yii::t('app', 'Торговать') ?></h1>
        <div class="row no-gutters c-trade-top__section">

            <div class="col-sm-12 col-lg-1">
            </div>
            <div class="col-sm-12 col-lg-4">
                <?= $this->render('/site/block/trade_mini') ?>
            </div>
            <div class="col-sm-12 col-lg-2">
            </div>
            <div class="col-sm-12 col-lg-4"  style="">
                <?= $this->render('/site/block/trade_profi') ?>
            </div>

        </div>
    </div>
</div>

<!--    Блок слайдер  -->
<?= $model['trede_static_page_slide'] ?>


