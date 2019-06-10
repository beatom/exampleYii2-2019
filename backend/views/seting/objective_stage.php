<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;use yii\bootstrap\ActiveForm;

$this->title = $model->id == null  ? 'Добавление уровня' : 'Цель до ' . $objective->max_sum . '$, уровень '. $model->stage . '%';

$this->params['breadcrumbs'][] = [  'label' => 'Настройки', 'url' => ['/seting/index']];
$this->params['breadcrumbs'][] = [  'label' => 'Цели', 'url' => ['/seting/objectives']];
$this->params['breadcrumbs'][] = [  'label' => 'Цель до '. $objective->max_sum . '$', 'url' => ['/seting/edit_objective/'.$objective->id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<h3>Добавить цель</h3>
<hr>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'form-add-objective-stage']); ?>

        <?= $form->field($model, 'stage')->input('number') ?>
        <?= $form->field($model, 'title')->input('text') ?>
        <?= $form->field($model, 'title_en')->input('text') ?>
        <?= $form->field($model, 'description')->textarea() ?>
        <?= $form->field($model, 'description_en')->textarea() ?>

        <div class="form-group">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary',]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

