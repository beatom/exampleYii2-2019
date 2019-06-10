<?php

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = ($model->meta_title)? $model->meta_title : $model->title;

if($model->meta_description){
    $this->registerMetaTag([
        'name' => 'description',
        'content' => $model->meta_description
    ]);
}
if($model->meta_keyword){
    $this->registerMetaTag([
        'name' => 'keywords',
        'content' => $model->meta_keyword
    ]);
}


//$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['/news/index']];
//$this->params['breadcrumbs'][] = $model->title;
?>
<div class="container">
	<section class="list-news">
		<div class="list-news__item">
			<div class="carousel__item--info__description"><a class="carousel__item--info__title" ><?= Html::encode($model->title) ?></a>
				<div class="carousel__item--info__from">
					<div class="carousel__item--info__from--sender"><?= Yii::t('app', 'От') ?>:<a href="<?= Url::to(['/news/index?from='.$model->from ]) ?>"><?= $model->from ?></a></div>
					<div class="carousel__item--info__from--date"><?= date('d.m.Y', strtotime($model->date_add)) ?></div>
					<div class="carousel__item--info__from--sender"><?= Yii::t('app', 'Категория') ?>:<a href="<?= Url::to(['/news/index?cat=' . $model->cat]) ?>"><?= $model->cat ?></a></div>
				</div>
				<div class="carousel__item--info__article">
					<?= $model->text_big ?>
					<?= $model->img ? '<img class="img-fluid" src="' . $model->img . '">' : null ?>
				</div>
			</div>
		</div>
	</section>
</div>
