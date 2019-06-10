<?php

use yii\helpers\Html;
use common\models\trade\TradingAccount;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
use common\models\User;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Промо баннеры', 'url' => ['/promo/index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['/promo/edit/' . $model->id]];
?>
<div class="row">

    <div class="col-lg-2">
        <?= $this->render('_sizes', ['banner_id' => $model->id, 'items' => $sizes]) ?>
    </div>
    <div class="col-lg-10">
        <div class="row">
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin(['id' => 'form-promo-banner']); ?>

                <?= $form->field($html_image, 'link')->input('text', ['value' => $html_image->link, 'placeholder' => '/img/promo/1'])->label('Ссылка на файл') ?>
                <?= $form->field($html_image, 'sizex')->input('text', ['value' => $html_image->sizex])->label('Ширина') ?>
                <?= $form->field($html_image, 'sizey')->input('text', ['value' => $html_image->sizey])->label('Высота') ?>
                <?= $form->field($html_image, 'is_main')->dropDownList([0 => 'Нет', 1 => 'Да'])->label('Основное изображение для заданных размеров?') ?>

                <div class="form-group">
                    <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
