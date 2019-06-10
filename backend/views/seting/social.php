<?php

// подключаем виджет постраничной разбивки
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Соц сети сайта';
?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

        <h1><?= $this->title ?></h1>


    <div class="body-content">

        <?php
        if($is_save){?>
            <p class="alert bg-success">Настройки сохранены</p><?php
        } ?>

        <div class="form-group">
            <?php $form = ActiveForm::begin(['id' => 'form-social']);?>

            <?= $form->field($model, 'facebook')->input('text')->label('facebook') ?>
            <?= $form->field($model, 'vk')->input('text')->label('vk') ?>
            <?= $form->field($model, 'twitter')->input('text')->label('twitter') ?>
            <?= $form->field($model, 'instagram')->input('text')->label('instagram') ?>
            <?= $form->field($model, 'youtube')->input('text')->label('youtube') ?>
            <?= $form->field($model, 'telegram')->input('text')->label('telegram') ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>
</div>
