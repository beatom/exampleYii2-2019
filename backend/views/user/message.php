<?php

use common\service\Servis;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;

$this->title = $model->id ? 'Редактирование сообщения' : 'Отправка сообщения';
$service = Servis::getInstance();
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = ['label' => 'Сообщения', 'url' => ['/user/messages/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
<div class="row">

    <div class="col-lg-2">
        <?= $this->render('menu', ['user_id' => $user->id]) ?>
    </div>
    <div class="col-lg-10">
        <?php
        if (!empty($accounts)) {
            ?>
            <?php $form = ActiveForm::begin(['id' => 'form-mass-messages']); ?>

            <h3><?= $this->title ?></h3>
            <?php
            foreach ($accounts as $account) {
                $s_id[$account->id] = $account->name . ' ' . $account->surname . ' (' . $account->position . ')';
            }
            echo $form->field($model, 'sender_id')->dropDownList($s_id)->label('Выберите отправителя');
            ?>
            <?= $form->field($model, 'title')->textInput()->label('Заголовок'); ?>
            <?= $form->field($model, 'text')->widget(Widget::class, [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 300,
                    'pastePlainText' => false,
                    'cleanSpaces' => false,
                    'replaceDivs' => false,
                    'cleanOnPaste' => false,
                    'buttonSource' => true,
                    'plugins' => [
                        'clips',
                        'fullscreen'
                    ],
                    'imageUpload' => false,
                    'fileUpload' => false
                ]
            ])->label(false); ?>


            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>


            <?php ActiveForm::end(); ?>

            <?php
        } else {
            echo '<p>Перед отправкой массовых сообщений необходимо создать аккаунт рассылки</p>';
        }
        ?>
    </div>
</div>