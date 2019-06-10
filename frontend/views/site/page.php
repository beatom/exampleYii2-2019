<?php

use yii\helpers\Html;

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

$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['/news/index']];
$this->params['breadcrumbs'][] = $model->title;
?>


<link rel="stylesheet" href="/css/news.css">


<div class="news-list">
    <h2><?= Html::encode($model->title) ?></h2>
    <div class="news-list__wrapper">
        <div class="news">
            <div class="single-news">
                <div class="news-list__item--description">
                    <?= $model->content ?>
                </div>
            </div>
        </div>
    </div>
</div>
