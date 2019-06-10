<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = Yii::t('app','Регистрация');
$this->params['breadcrumbs'][] = $this->title;


$template = '<div class="row align-items-baseline"><div class="col-md-3">{label}</div><div class="col-md-9 px-0">{input}{error}</div></div>';


$form = ActiveForm::begin(['id' => 'form-signup' ]);

$sms_blocked = Yii::$app->request->cookies->get('registration_sms_block');
$sms_blocked_class = $sms_blocked  ? '' : 'display:none;';
$sms_blocked_button = $sms_blocked ? 'sms_blocked' : '';
?>


<div class="registration-form display_table">
    <div class="form-item">
        <div class="form-item--title">Регистрация</div>
        <div class="registration step-one" style="<?= $step1 ? null : 'display: none'  ?>">
            <div class="col">
                <?= $form->field($model, 'username',[ 'template' => $template ])
                    ->textInput([
                        'placeholder' => 'Придумайте логин',
                        'class' => 'form-input'
                    ])->label('Логин') ?>

                <?= $form->field($model, 'phone',[
                        'template' => $template ])
                    ->textInput([
                        'placeholder' => 'Введите номер телефона',
                        'class' => 'form-input'
                    ])
                    ->label('Телефон') ?>


                <?= $sms_enabled ? $form->field($model, 'sms_code',[
                        'template' => '<div class="row align-items-baseline"><div class="col-md-3">{label}</div><div class="col-md-9 px-0"><div class="row form-code"><div class="col-md-7">{input}{error}</div><div class="col-md-5"><a id="get-registration-sms" class="get-sms '.$sms_blocked_button.'"  href="#">Выслать смс код</a></div></div><small id="sms_code_info" style="' .$sms_blocked_class. '" class="form-text">Вы слишком часто запрашивали код. Попробуйте через 5 минут.</small></div></div>'
                    ])
                    ->textInput(['placeholder' => 'Смс код', 'class' => 'form-input'])
                    ->label('Смс код') : '' ?>


                <div class="row">
                    <div class="col">
                        <button id="go_step_two" class="default-btn">Готово</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="registration step-two" style="<?= $step1 ? 'display: none' : null ?>">
            <div class="col">
                <?= $form->field($model, 'firstname',[ 'template' => $template ])
                    ->textInput([
                        'placeholder' => 'Введите своё имя',
                        'class' => 'form-input'
                    ])->label('Имя') ?>


                <?= $form->field($model, 'email',[ 'template' => $template ])
                    ->textInput([
                        'placeholder' => 'Введите свою почту',
                        'class' => 'form-input'
                    ])->label('Почта') ?>

                <?= $form->field($model, 'password',[ 'template' => $template ])
                    ->passwordInput([
                        'placeholder' => 'Введите свой пароль',
                        'class' => 'form-input'
                    ])->label('Пароль') ?>

                <?= $form->field($model, 'promo_code',[
                    'template' =>  '<div class="row align-items-baseline"><div class="col-md-3">{label}</div><div class="col-md-9 px-0">{input}{error}<small class="form-text">*Промо-код обеспечивает 25% скидку на оплату invest</small></div>'
                ])
                    ->textInput([
                        'placeholder' => 'Введите промо-код',
                        'class' => 'form-input'
                    ])->label('Промо-код') ?>


                <div class="row">
                    <div class="col">
                        <?= Html::submitButton(Yii::t('app', 'Готово'), ['class' => 'default-btn']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>