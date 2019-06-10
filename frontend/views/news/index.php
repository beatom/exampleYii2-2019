<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = $seo['title'];
?>


<div class="container">
    <section class="list-news">
        <?php
        $servis = \common\service\Servis::getInstance();
        foreach ($models as $item) {
            $item = $servis->translete($item);
            ?>
            <div id="n<?= $item->id ?>" class="list-news__item <?= $selected_id == $item->id ? 'gototag' : null ?>>">
                <div class="carousel__item--info__description"><a class="carousel__item--info__title nothing" href="#"><?= $item->title ?></a>
                    <div class="carousel__item--info__from">
                        <div class="carousel__item--info__from--sender"><?= Yii::t('app', 'От') ?>:<a class="nothing" href="#"><?= $item->from ?></a></div>
                        <div class="carousel__item--info__from--date"><?= date('d.m.Y', strtotime($item->date_add)) ?></div>
                        <div class="carousel__item--info__from--sender"><?= Yii::t('app', 'Категория') ?>:<a class="nothing" href="#"><?= $item->cat ?></a></div>
                    </div>
                    <div class="carousel__item--info__article" data-text="<?= $item->text_big ?>">
                        <?php if($selected_id == $item->id )  {
                            echo $item->text_big;
                        } else { ?>
                        <p>
                            <?= mb_substr(strip_tags($item->text_big), 0, 500, "UTF-8") ?>... <a class="read-more" href="" ><?= Yii::t('app', 'читать далее') ?></a>
                        </p>
                        <?php } ?>
                        <?= $item->img ? '<img class="img-fluid" src="' . $item->img . '">' : null ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
</div>


<?php
// отображаем постраничную разбивку
echo LinkPager::widget([
    'pagination' => $pages,
    'options' => ['class' => 'table__pagination',],
    'activePageCssClass' => 'is-active',
    'linkContainerOptions' => ['class' => 'table__pagination-item'],
    'linkOptions' => ['class' => 'table__pagination-link'],
    'prevPageLabel' => '',
    'nextPageLabel' => '',
]); ?>

<script>

</script>