<?php

use common\models\User;
use common\service\Servis;
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = Yii::t('cab', 'Промо-материалы');

//$service = Servis::getInstance();
//
//$arrChangeBall = $service->getArrChangeBall();
//$data = $service->getArrPartnerStatus();
//
//$size_get = null;
//$size_value = false;
//$format_get = null;
//$format_value = false;

//var_dump($materials);
//var_dump($size_value);
//var_dump($format_value);

?>


<div class="content col pt-0">
    <div class="promo">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" id="banner-tab" data-toggle="tab" href="#png" role="tab" aria-controls="png" aria-selected="true">Статичные баннера</a></li>
            <li class="nav-item"><a class="nav-link" id="gif-tab" data-toggle="tab" href="#gif" role="tab" aria-controls="gif" aria-selected="false">Гиф-Баннера</a></li>
            <li class="nav-item"><a class="nav-link" id="html-tab" data-toggle="tab" href="#html" role="tab" aria-controls="html" aria-selected="false">HTML-баннера</a></li>
            <li class="nav-item"><a class="nav-link" id="video-tab" data-toggle="tab" href="#video" role="tab" aria-controls="video" aria-selected="false">Видео</a></li>
        </ul>
        <div class="tab-content">
            <?php
            $i = 1;
            foreach ($materials as $key1 => $types) { ?>
                <div class="tab-pane fade <?= $i++ == 1 ? 'show active' : null ?>" id="<?= $key1 ?>" role="tabpanel" aria-labelledby="<?= $key1 ?>-tab">
                    <div class="promo-content">
                        <div class="promo-content__top" data-target=".grid-<?= $key1 ?>">
                            <?php if (count($types) > 1) { ?>
                            <div class="promo-content__top--title">Размеры:</div>
                                <button class="button is-checked" data-filter="*">Все</button>
                                <?php foreach ($types as $key2 => $images) { ?>
                                    <button class="button" data-filter=".<?= $key2 ?>"><?= $key2 ?></button>
                                <?php }
                            } ?>
                        </div>
                        <div class="grid grid-<?= $key1 ?>">
                            <?php foreach ($types as $key2 => $images) {
                                foreach ($images as $image) { ?>
                                    <div class="element-item <?= $key2 ?>" data-category="<?= $key2 ?>">
                                        <div data-toggle="modal" data-target="#show-promo" data-size="<?= $image['html_size'] ?>" data-promo="<?= htmlentities($image['preview']) ?>" class="to_promo_banner banner-img" style="background-image: url(&quot;<?= $image['download'] ?>&quot;)">
                                           <?= $key1 == 'html' ? '<p>Кликни для просмотра</p>' : null ?>
                                        </div>
                                        <div class="banner-info">
                                            <div class="banner-info__code">
                                                <button class="banner-info__code--copy" type="submit"></button>
                                                <code class="banner-info__code--code"><?= $image['partner_link'] ?></code>
                                            </div>
                                            <a class="default-btn" href="<?= $image['download'] ?>" download="">скачать</a>
                                        </div>
                                    </div>
                                <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="tab-pane fade" id="video" role="tabpanel" aria-labelledby="video-tab">
                <div class="promo-content">
                    <div class="promo-content__video__item">
                        <div class="promo-content__video">
                            <div class="promo-content__video--img" style="background-image: url(&quot;/img/promo_video1_preview.jpg&quot;)">
                                <div class="promo-content__video--play">
                                    <div class="promo-content__video--play__btn" data-toggle="modal" data-target="#show-video" data-video="https://www.youtube.com/embed/D2pDzJK3_84" data-title=""></div>
                                </div>
                                <a class="promo-content__video--download" href="/img/promo/invest_promo.mp4" data-toggle="tooltip" data-placement="right" data-html="true" title="&lt;div class='download-tooltip'&gt;скачать&lt;/div&gt;" download=""></a>
                            </div>
                        </div>
                        <div class="promo-content__video">
                            <div class="promo-content__video--img" style="background-image: url(&quot;/img/promo_video_2_wall.png&quot;)">
                                <div class="promo-content__video--play">
                                    <div class="promo-content__video--play__btn video_local" data-video="/img/promo/invest_promo2.mp4" data-width="1280" data-height="720" data-title=""></div>
                                </div>
                                <a class="promo-content__video--download" href="/img/promo/invest_promo2.mp4" data-toggle="tooltip" data-placement="right" data-html="true" title="&lt;div class='download-tooltip'&gt;скачать&lt;/div&gt;" download=""></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--    <div class="modal fade" id="show-promo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">-->
<!--        <div class="modal-dialog show-video__modal" role="document" style="max-width: 750px;">-->
<!--            <div class="modal-body">-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->


<?= $this->render('@app/views/layouts/_video_popup') ?>