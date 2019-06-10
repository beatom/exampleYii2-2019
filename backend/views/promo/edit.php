<?php

use yii\helpers\Html;
use common\models\trade\TradingAccount;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
use common\models\User;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Промо баннеры', 'url' => ['/promo/index']];

?>
<div class="row">
    <?php if(isset($sizes) AND isset($model->id)) { ?>
    <div class="col-lg-2">
        <?= $this->render('_sizes', ['banner_id' => $model->id, 'items' => $sizes]) ?>
    </div>
    <div class="col-lg-10">
        <?php } ?>
        <div class="row">
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin(['id' => 'form-promo-banner']); ?>

                <?= $form->field($model, 'name')->input('text',['value' => $model->name ])->label('Название') ?>
                <?= $form->field($model, 'folder')->input('text',['value' => $model->folder, 'placeholder' => '/img/promo/1' ])->label('Путь к папке на сервере') ?>
                <?= $form->field($model, 'show')->dropDownList([0 => 'Нет', 1 => 'Да'])->label('Отображать пользователям') ?>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
        <?php if(isset($types)) {
            echo '<hr>Типы файлов<br><pre>';
           foreach ($types as $t) {
               echo "$t\n";
           }
            echo '</pre>';
        }
         if($model->id) {
            echo '</div>';
        }
        ?>


    </div>
