<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\BalanceLog;

$this->title = 'Редактирование заявки';
$this->params['breadcrumbs'][] = [  'label' => 'Движение средств', 'url' => ['/money_log']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-change-transfer']); ?>

            <?= Html::input('hidden', 'ChangeTransferForm[id]', $model->id) ?>
            <?= Html::input('hidden', 'ChangeTransferForm[user_id]', $model->user_id) ?>
            <?= Html::input('hidden', 'ChangeTransferForm[operation]', $model->operation) ?>
            <?= Html::input('hidden', 'ChangeTransferForm[system]', $model->system) ?>

            <p>Дата заявки <b><?= $model->date_add ?></b></p>
            <p>Операция: <b><?= BalanceLog::$operation_name[$model->operation] ?></b></p>

            <?php
            if(isset($helper['recipient'])){
                echo '<p>Пользователь <b>'.$helper['sender']->username.'</b> заказал перевод личных средств пользователю <b>'.$helper['recipient']->username.'</b></p>';
            } ?>



            <?= $form->field($model, 'summ')->input('text',[
                'readonly'=>'readonly',
                'value' => $model->summ,
            ])->label('Сумма') ?>

            <?= $form->field($model, 'comment')->input('text',[
                'value' => $model->comment,
            ])->label('Комментарий') ?>

            <?= $form->field($model, 'status')->dropDownList( BalanceLog::getAdminChangeTranserOperations() )->label('Статус')?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
