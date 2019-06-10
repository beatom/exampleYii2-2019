<?php

// подключаем виджет постраничной разбивки
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = 'Данные страницы "О компании"';
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
            <?php $form = ActiveForm::begin(['id' => 'form-about']);?>

            <?= $form->field($model, 'about_speed_request')->input('text')->label('Средняя скорость ответа поддержки') ?>
            <?= $form->field($model, 'about_alternative_banks')->input('text')->label('более выгодная альтернатива банкам') ?>
            <?= $form->field($model, 'about_paid_month')->input('text')->label('Выплачено за месяц') ?>
            <?= $form->field($model, 'about_count_partner')->input('text')->label('Партнёров invest') ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>
</div>
