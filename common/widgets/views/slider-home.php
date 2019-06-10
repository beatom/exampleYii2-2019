<?php

use common\service\Servis;
use yii\helpers\Url;
?>

<div class="c-main main-slider__wrapper">
    <!-- new main slider -->
    <div class="main-slider">
        <div class="main-slider__video">
            <video width="100%" height="auto" playsinline autoplay muted loop>
                <source src="/img/16829389-hd.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
            </video>
        </div>
        <div class="container">
            <div class="main-slider__list">
                <div class="main-slider__list--items">
                    <div class="main-slider__list--title">Международная брокерская  компания invest24</div>
                    <ul class="main-slider__list--dots">
                        <li class="active"></li>
                        <li></li>
                    </ul>
                </div>
                <div class="slide-info">
                    <div class="tabgroup" id="first-tab-group" >
                        <div id="about" data-id="#about" class="main-slider__list--description active">
                            <div class="main-slider__list--item fade-in">Трейдинг и инвестирование в высокодоходный  рынок Форекс!</div>
                            <div class="main-slider__list--item fade-out">Представление брокерских услуг с 2017 года</div>
                        </div>
                        <div id="invest" data-id="#invest" class="main-slider__list--description">
                            <div class="main-slider__list--item fade-in">На выбор более 50-ти управляющих  трейдеров</div>
                            <div class="main-slider__list--item fade-out">Инвестиционные продукты с ежедневными  дивидендами</div>
                        </div>
                        <div id="trade" data-id="#trade" class="main-slider__list--description">
                            <div class="main-slider__list--item fade-in">Собственный учебный центр по трейдингу</div>
                            <div class="main-slider__list--item fade-out">Более 100 инструментов для торговли</div>
                        </div>
                    </div>
                    <ul class="tabs clearfix" data-tabgroup="first-tab-group">
                        <li>
                            <a href="#about" class="active">
                                <div class="main-slider__tab">
                                    <div class="main-slider__tab--item">о компании</div>
                                    <div class="slide-progress--item">
                                        <div class="slide-progress__bg"></div>
                                        <div class="slide-progress"></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#invest">
                                <div class="main-slider__tab">
                                    <div class="main-slider__tab--item">Инвестирование</div>
                                    <div class="slide-progress--item">
                                        <div class="slide-progress__bg"></div>
                                        <div class="slide-progress"></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a href="#trade">
                                <div class="main-slider__tab">
                                    <div class="main-slider__tab--item">Торговля</div>
                                    <div class="slide-progress--item">
                                        <div class="slide-progress__bg"></div>
                                        <div class="slide-progress"></div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <!--<div class="slider slider-for">
                    <div>
                        <div class="slider inner-slider">
                            <div>
                                <div class="main-slider__list--item">Трейдинг и инвестирование в высокодоходный  рынок Форекс!</div>
                            </div>
                            <div>
                                <div class="main-slider__list--item">Представление брокерских услуг с 2017 года</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="slider inner-slider">
                            <div>
                                <div class="main-slider__list--item">На выбор более 50-ти управляющих  трейдеров</div>
                            </div>
                            <div>
                                <div class="main-slider__list--item">Инвестиционные продукты с ежедневными  дивидендами</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="slider inner-slider">
                            <div>
                                <div class="main-slider__list--item">Собственный учебный центр по трейдингу</div>
                            </div>
                            <div>
                                <div class="main-slider__list--item">Более 100 инструментов для торговли</div>
                            </div>
                        </div>
                    </div>
                </div>-->
                <!--<div class="slider slider-nav">
                    <div class="main-slider__tab">
                        <div class="main-slider__tab--item">о компании</div>
                        <div class="slide-progress--item">
                            <div class="slide-progress__bg"></div>
                            <div class="slide-progress"></div>
                        </div>
                    </div>
                    <div class="main-slider__tab">
                        <div class="main-slider__tab--item">Инвестирование</div>
                        <div class="slide-progress--item">
                            <div class="slide-progress__bg"></div>
                            <div class="slide-progress"></div>
                        </div>
                    </div>
                    <div class="main-slider__tab">
                        <div class="main-slider__tab--item">Торговля</div>
                        <div class="slide-progress--item">
                            <div class="slide-progress__bg"></div>
                            <div class="slide-progress"></div>
                        </div>
                    </div>
                </div>-->
                <div class="main-slider__list__btns">
                    <a href="#" class="c-btn invest-btn">инвестировать</a>
                    <a href="#" class="c-btn create-account">Создать счёт</a>
                </div>
            </div>
        </div>
    </div>
    <!-- end new main slider -->

    <!--<div class="u-pf">
        <div class="c-main__slider">
            <div class="u-slider u-single-item"><?php
/*
                $flag = true;
                foreach ($slids as $slide){
                    $slide = Servis::getInstance()->translete($slide);
                    */?>
                    <div class="u-slider-item">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="u-slider__article">
                                        <div class="b-h1">
                                            <?/*= $slide->title */?>
                                        </div>
                                        <p>
                                          <?/*= $slide->subtitle */?>
                                        </p>

                                        <a href="<?/*= in_array(substr($slide->url, 0, 2), ['ht']) ? $slide->url : Url::to( [$slide->url]) */?>" title="" class="c-btn is-bg-color-black u-btn-start-now">
                                            <span><?/*= $slide->text_button */?></span>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-6 col-lg-6 align-self-end">

                                    <div class="u-slider__img">
                                        <img src="<?/*= $slide->img */?>" alt="">
                                    </div>
                                </div>

                                <?php
/*                                if ($flag){
                                    $flag=false; */?>

                                    <div class="u-slider__circle__row" id="scene">
                                        <div class="u-slider__circle__row__layer" data-depth="0.30">
                                            <div class="u-slider__circle is-circle-boy">
                                                <span class="u-slider__other-img" style="background-image: url(/img/main/slider/u-slider-circle-boy.png);"></span>
                                                <span class="u-slider__other-circle"></span>
                                            </div>
                                        </div>

                                        <div class="u-slider__circle__row__layer" data-depth="0.20">
                                            <div class="u-slider__circle is-circle-girl">
                                                <span class="u-slider__other-img" style="background-image: url(/img/main/slider/u-slider-circle-girl.png);"></span>
                                                <span class="u-slider__other-circle"></span>
                                            </div>
                                        </div>
                                    </div><?php
/*                                }
                                */?>
                            </div>
                        </div>
                    </div><?php
/*                } */?>

            </div>

            <div class="u-slider-progressbar__row">
                <ul class="u-slider-progressbar">
                    <li>
						<span class="slick-prev slick-arrow">
							<svg width="10" height="13">
					      <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/img/svgs.svg#i-arrow-top"></use>
					    </svg>
						</span>
                    </li><?php
/*                    for($i=0; $i < count($slids); $i++){
                        echo '<li><span data-slick-index="'.$i.'" class="u-slider-progressbar__item"></span></li>';
                    }
                    */?>
                    <li>
			    	<span class="slick-next slick-arrow">
							<svg width="10" height="13">
					      <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/img/svgs.svg#i-arrow-bottom"></use>
					    </svg>
						</span>
                    </li>
                </ul>
            </div>


            <div class="u-slide-count-wrap">
                <span class="u-current"></span>
                <span class="u-total"></span>
            </div>
        </div>

<!--        <div class="c-main-sn wow fadeInDown" data-wow-delay="0.5s">-->
<!---->
<!--            <ul>-->
<!--                <li><a href="--><?/*//= $social['vk'] */?><!--" title="">Вконтакте</a></li>-->
<!--                <li><a href="--><?/*//= $social['facebook'] */?><!--" title="">Facebook</a></li>-->
<!--                <li><a href="--><?/*//= $social['instagram'] */?><!--" title="">Instagram</a></li>-->
<!--                <li><a href="--><?/*//= $social['youtube'] */?><!--" title="">Youtube</a></li>-->
<!--            </ul>-->
<!--        </div>-->
    <!--</div>-->
</div>


