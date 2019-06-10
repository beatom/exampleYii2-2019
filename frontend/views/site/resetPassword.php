<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app','Введите новый пароль');

?>
<div class="registration-form">
    <div class="form-item">
        <div class="registration step-one">
            <?php
            $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <div class="form-item--title"><?= Yii::t('app', 'Введите новый пароль') ?></div>
            <div class="col">


                <?= $form->field($model, 'password')->passwordInput([
                    'autofocus' => true,
                    'placeholder' => 'Пароль',
                    'class' => 'form-input'
                ])->label('Пароль') ?>


                <div class="row">
                    <div class="col">
                        <?= Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'default-btn', 'name' => 'login-button']) ?>
                    </div>
                </div>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
