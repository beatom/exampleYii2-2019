<?php
use common\service\Servis;
use yii\helpers\Url;
?>

<div class="last-news">
    <div class="container">
        <h2><?= Yii::t('app', 'Последние новости компании') ?></h2>
        <section class="carousel"><a class="carousel__arrow carousel__arrow--left" href="#!"></a>
            <div class="carousel__container slider responsive">
                <?php
                $i = 0;
                foreach ($news as $item){
                    $item = Servis::getInstance()->translete($item)
                    ?>
                    <div class="carousel__item <?= $i==0 ? 'carousel__container--active' : null ?>" data-target="<?=  $i++ ?>">
                        <div class="carousel__item--img-top" style="background-image: url(&quot;<?= $item->img ?>&quot;)"></div>
                        <div class="carousel__item--info">
                            <div class="carousel__item--info__date"><?= date('d<\s\p\a\n>m, Y', strtotime($item->date_add)) ?></span></div>
                            <div class="carousel__item--info__description"><a class="carousel__item--info__title" href="<?= Url::to(['/news/index?id='.$item->id.'#n'.$item->id]) ?>"><?= $item->title ?></a>
                                <div class="carousel__item--info__from">
                                    <div class="carousel__item--info__from--sender"><?= Yii::t('app', 'От') ?>:<a class="nothing" href="#"><?= $item->from ?></a></div>
                                    <div class="carousel__item--info__from--date"><?= date('d.m.Y', strtotime($item->date_add)) ?></div>
                                    <div class="carousel__item--info__from--sender"><?= Yii::t('app', 'Категория') ?>:<a class="nothing" href="#"><?= $item->cat ?></a></div>
                                </div>
                                <div class="carousel__item--info__article" style="cursor: default;">
                                    <p>
                                        <?= mb_substr( $item->text_big, 0, 500, "UTF-8") ?>... <a href="<?= Url::to(['/news/index?id='.$item->id.'#n'.$item->id]) ?>"><?= Yii::t('app', 'читать далее') ?></a>
                                    </p>
                                    <a class="discuss show__chat" href="#"><?= Yii::t('app', 'ОБСУДИТЬ') ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php      } ?>
            </div>
            <a class="carousel__arrow carousel__arrow--right" href="#!"></a>
        </section>
        <div class="d-flex"><a class="last-news__link" href="<?= Url::to(['/news/index']) ?>"><?= Yii::t('app', 'посмотреть все новости') ?></a></div>
    </div>
</div>

