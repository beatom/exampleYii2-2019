<?php

use yii\helpers\Html;

$this->title = 'Настройки сайта';
?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

        <h1><?= $this->title ?></h1>

    <div class="body-content">
        <div class="form-group">
            <h3>Курс $=C</h3>
            <?= Html::beginForm('#', 'post', ['name'=>'save-kurs']) ?>
            1$ = <?= Html::input('number', 'ball_rate', $exchange_rate[1]) ?>
            <?= Html::submitButton('Cохранить',['class'=> 'btn btn-primary'])?>
        </div>
        <div class="form-group">
            <?= Html::beginForm('#', 'post', ['name'=>'save-card_number']) ?>
            Номер карты для перевода пользователями (разделяются 2-мя пробелами)<br>
            <?= Html::input('text', 'card_number', $card_number) ?>
            <?= Html::submitButton('Cохранить',['class'=> 'btn btn-primary'])?>
        </div>
        <hr>
        <div class="form-group">
            <h3>Яндекс метрика</h3>
            <?= Html::beginForm('#', 'post', ['name'=>'save-metrics']) ?>
            <?= Html::textarea('yandex_metrica', $yandex_metrica, ['cols' => 80, 'rows' => 10]) ?>
            <br>
            <?= Html::submitButton('Cохранить',['class'=> 'btn btn-primary'])?>
        </div>
        <div class="form-group">
            <h3>Код Jivosite</h3>
            <?= Html::beginForm('#', 'post', ['name'=>'save-metrics']) ?>
            <?= Html::textarea('jivosite_code', $jivosite_code, ['cols' => 80, 'rows' => 10]) ?>
            <br>
            <?= Html::submitButton('Cохранить',['class'=> 'btn btn-primary'])?>
        </div>
    </div>
</div>
