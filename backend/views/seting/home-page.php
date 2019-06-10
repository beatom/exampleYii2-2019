<?php

// подключаем виджет постраничной разбивки
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use vova07\imperavi\Widget;

$this->title = 'Настройки главной страницы';
?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

        <h1><?= $this->title ?></h1>

    <div class="body-content">

        <?php
        if($is_save){?>
            <p class="alert bg-success">Настройки сохранены</p><?php
        } ?>

        <?php $form = ActiveForm::begin(['id' => 'form-home-page','options' => ['enctype' => 'multipart/form-data']]);?>

        <div class="alert-warning alert">
            Слайдер добавляется в <a href="<?= Url::to('/banner/main') ?>">баннерах</a>. Позиция "На главной"
            <br>
            Блок "Представляем" настраивается в <a href="<?= Url::to('/seting/invest') ?>">настройки->инвестировать</a>
        </div>



        <div class="form-group">
            <?= $form->field($model, 'home_simple_as')->textarea(['rows'=> 7])->label('Блок "ЭТО ПРОСТО КАК"');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'home_simple_as_en')->textarea(['rows'=> 7])->label('Блок "ЭТО ПРОСТО КАК" EN');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'wideo_on_home')->textarea(['rows'=> 7])->label('Блок с видео');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'wideo_on_home_en')->textarea(['rows'=> 7])->label('Блок с видео EN');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'why_invest')->textarea(['rows'=> 7])->label('Почему invest');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'why_invest_en')->textarea(['rows'=> 7])->label('Почему invest EN');?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
        </div>

        <?php ActiveForm::end(); ?>


    </div>
</div>
