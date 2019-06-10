<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Сброс пароля');
//$model->message = 'Проверьте свою электронную почту для получения дальнейших инструкций.';
?>


<div class="registration-form">
    <div class="form-item">
        <div class="registration step-one">
            <?php
            $form = ActiveForm::begin(['id' => 'login-form',
                //'fieldConfig' => ['options' => ['class' => 'form-group d-flex justify-content-center flex-column']],
            ]); ?>
            <div class="form-item--title"><?= Yii::t('app', 'Пожалуйста, заполните свой адрес электронной почты. Будет отправлена ссылка на сброс пароля') ?></div>
            <div class="col">

                <?php if ($model->message) {
                    echo '<p style="color: green;text-align: center">' . $model->message . '</p>';
                } else { ?>
                    <?= $form->field($model, 'email')->textInput([
                        'autofocus' => true,
                        'placeholder' => 'Email',
                        'class' => 'form-input'
                    ]) ?>

                    <div class="row">
                        <div class="col">
                            <?= Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'default-btn', 'name' => 'login-button']) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
