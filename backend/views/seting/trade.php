<?php

// подключаем виджет постраничной разбивки
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use vova07\imperavi\Widget;

$this->title = 'Настройки страницы "Торговать"';
?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

        <h1><?= $this->title ?></h1>

    <div class="body-content">

        <?php
        if($is_save){?>
            <p class="alert bg-success">Настройки сохранены</p><?php
        } ?>

        <div class="alert-warning alert">
            Блок c планами настраивается в <a href="/seting/trade_plan">планах</a>
        </div>

        <?php $form = ActiveForm::begin(['id' => 'form-home-page','options' => ['enctype' => 'multipart/form-data']]);?>


        <div class="form-group">
            <?= $form->field($model, 'trede_static_page_slide')->textarea(['rows'=> 7])->label('Блок SLIDER');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'trede_static_page_slide_en')->textarea(['rows'=> 7])->label('Блок SLIDER EN');?>
        </div>



        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
        </div>

        <?php ActiveForm::end(); ?>



    </div>
</div>
