<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'SMS провайдеры', 'url' => ['/sms_settings']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?=Html::encode($this->title)?></h1>

    <div class="row">
        <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'form-sms']);?>
        
        <?=$form->field($model, 'name')->textInput(['readonly' => true])->label('Название')?>
        <?=$form->field($model, 'api_login')->input('text')->label('Логин Api')?>
        <?=$form->field($model, 'api_password')->input('text')->label('Пароль Api')?>
        <br>
        <div class="form-group">
            <?=Html::submitButton('Обновить', ['class' => 'btn btn-primary', 'name' => 'mydata'])?>
        </div>

        <?php ActiveForm::end();?>
        </div>
    </div>

</div>
