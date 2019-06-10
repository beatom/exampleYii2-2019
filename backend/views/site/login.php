<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Введите необходимые даные для доступа в административную панель:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']);


            if($model->stage == 1) { ?>
                <?= $form->field($model, 'username')->textInput(['autofocus' => true])->label('Логин') ?>
                <?= $form->field($model, 'password')->passwordInput()->label('Пароль') ?>
                <?= $form->field($model, 'rememberMe')->checkbox()->label('Запомнить меня') ?>
                <?= $form->field($model, 'stage')->hiddenInput()->label(false) ?>
            <?php } else { ?>
            <?= $form->field($model, 'username')->hiddenInput(['autofocus' => true])->label(false) ?>
            <?= $form->field($model, 'password')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'rememberMe')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'stage')->hiddenInput()->label(false) ?>
            <?= $form->field($model, 'sms_code')->textInput(['autofocus' => true]) ?>
            <?php } ?>

                <div class="form-group">
                    <?= Html::submitButton('Ввойти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
