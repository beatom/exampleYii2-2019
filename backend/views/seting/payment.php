<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;
use common\service\Servis;
use kartik\widgets\DateTimePicker;

$this->title = 'Платежная система ' . $model->title;

$service = Servis::getInstance();
$this->params['breadcrumbs'][] = ['label' => 'Настройки', 'url' => ['/seting/index']];
$this->params['breadcrumbs'][] = ['label' => 'Платежные системы', 'url' => ['/seting/payments']];
$this->params['breadcrumbs'][] = $this->title;




?>
<div class="site-signup">
    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">
        <div class="col-lg-12">
            Платежная система:  <?= $model->system ?><br>
            Валюта: <?= $model->currency->synonym ?><br>
            <hr>
            <?php $form = ActiveForm::begin(['id' => 'form-add-news', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'title')->input('text') ?>

            <?= $form->field($model, 'sum_min')->input('number') ?>
            <?= $form->field($model, 'sum_max')->input('number') ?>


            <?= $form->field($model, 'fee')->input('text', ['value' => $service->beautyDecimal($model->fee)]) ?>
            <?= $form->field($model, 'fee_verified')->input('text', ['value' => $service->beautyDecimal($model->fee_verified)]) ?>
            <?= $form->field($model, 'fee_add')->input('text', ['value' => $service->beautyDecimal($model->fee_add)]) ?>


            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
