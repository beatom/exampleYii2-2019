<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Авторизация');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="registration-form">
    <div class="form-item">
        <div class="form-item--title">Быстрый вход</div>
        <ul class="qwick-enter">
            <li><a class="link" href="https://oauth.vk.com/authorize?client_id=<?= \common\service\Servis::id_vk ?>&display=page&redirect_uri=<?= Url::home(true) ?>site/vklogin&scope=email,offline&response_type=token&v=5.52"><span>
                <svg class="vk-ico">
                  <use xlink:href="/img/sprites/sprite.svg#vk-ico"></use>
                </svg></span><?= Yii::t('app', 'Вконтакте') ?></a></li>
            <li><a class="link facebook" onclick="myFacebookLogin();return false;" href="#"><span>
                <svg class="facebook-ico">
                  <use xlink:href="/img/sprites/sprite.svg#facebook-ico"></use>
                </svg></span>Facebook</a></li>
        </ul>
        <div class="registration step-one">
            <?php
            $form = ActiveForm::begin(['id' => 'login-form',
                //'fieldConfig' => ['options' => ['class' => 'form-group d-flex justify-content-center flex-column']],
            ]); ?>
            <div class="form-item--title">Авторизация</div>
            <div class="col">


                <?= $form->field($model, 'username',
                    ['template' => '<div class="row align-items-baseline"><div class="col-md-4">{label}</div><div class="col-md-8 px-0">{input}{error}</div></div>'])
                    ->input('text', [
                            'placeholder' => 'Логин',
                            'autocomplete' => "off",
                            'class' => 'form-input'
                        ]
                    )->label(Yii::t('app', 'Логин')) ?>

                <?= $form->field($model, 'password', [
                    'template' => '<div class="row align-items-baseline"><div class="col-md-4">{label}</div><div class="col-md-8 px-0 d-flex flex-column align-items-end">{input}{error}<a class="forgot-pass" href="' . Url::to(['/site/request-password-reset']) . '">Забыл пароль</a></div></div>'
                ])->passwordInput([
                    'placeholder' => 'Пароль',
                    'class' => 'form-input'
                ])->label(Yii::t('app', 'Пароль')) ?>


                <div class="row">
                    <div class="col">
                        <?= Html::submitButton(Yii::t('app', 'Готово'), ['class' => 'default-btn', 'name' => 'login-button']) ?>
                        <a class="registration-link" href="<?= Url::to(['/site/signup']) ?>">Регистрация</a>
                    </div>
                </div>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
