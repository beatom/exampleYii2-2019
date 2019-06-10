<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;
use common\service\Servis;
use kartik\widgets\DateTimePicker;

$this->title = $days_log ? $seo['title'] . 'к дате ' . $days_log->date_add : $seo['title'];

$service = Servis::getInstance();
$this->params['breadcrumbs'][] = ['label' => 'Статистика по дням', 'url' => ['/events/index']];
$this->params['breadcrumbs'][] = $this->title;


if(date('H') < 10) {
    $start_date = date('Y-m-d 15:00', strtotime(' -1 day'));
} else {
    $start_date = date('Y-m-d 15:00');
}

?>
<div class="site-signup">
    <h3><?= Html::encode($this->title) ?></h3>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-add-news', 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'title')->input('text') ?>

            <?php
            $plugin_options = [
                'format' => 'yyyy-MM-dd hh:i:s',
                'autoclose' => true,
                'weekStart' => 1, //неделя начинается с понедельника
                'startDate' => $start_date, //самая ранняя возможная дата
                'todayBtn' => true, //снизу кнопка "сегодня"
            ];
            if($days_log) {
                $plugin_options['endDate'] = date('Y-m-d 09:59:59', strtotime($days_log->date_add . ' +1 day'));
            }
            echo $form->field($model, 'date_add')->widget(DateTimePicker::class, [
                'name' => 'dp_1',
                'type' => DateTimePicker::TYPE_INPUT,
                'options' => ['placeholder' => 'Ввод даты/времени...'],
                'convertFormat' => true,
                'value' => date("Y-m-d h:i:s", (integer)$model->date_add),
                'pluginOptions' => $plugin_options
            ]) ?>
            <?= $form->field($model, 'bank_percent')->input('number') ?>
            <?= $form->field($model, 'bet')->input('text') ?>
            <?= $form->field($model, 'coefficient')->input('text') ?>
            <?= $form->field($model, 'bookmaker')->input('text') ?>

            <?= $form->field($model, 'result')->dropDownList(\common\models\Events::$results) ?>
            <?= $form->field($model, 'free')->dropDownList([0 => 'закрыт', 1 => 'открыт']) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
