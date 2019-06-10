<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;
use common\models\trade\TradingAccount;
use kartik\widgets\DatePicker;
use common\models\trade\TradingPeriodLog;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = ['label' => 'Отзывы об synergy', 'url' => ['/user/solution-reviews/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;

$marks = [
    1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5
];
?>

<div class="site-signup">
    <h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
    <div class="row">
        <div class="col-lg-2">
            <?= $this->render('menu', ['user_id' => $user->id]) ?>
        </div>

        <div class="col-lg-10">
            <div class="row">
                <div class="col-lg-12">
                    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                    <?= $form->field($model, 'solution_id')->text($trading_accounts, ['class' => 'form-control select__account', 'readonly' => ($model->solution_id AND $model->id) ? 'true' : false])->label('Готовое решение') ?>
                    <!--                    --><?//= $form->field($model, 'traiding_period_log_id')->dropDownList($periods, ['class' => 'form-control select__period', 'options' => $period_opdions, 'disabled' => ($model->traiding_period_log_id AND $model->id) ? true : false])->label('Торговый период') ?>
                    <?= $form->field($model, 'user_id', ['errorOptions' => ['class' => 'help-block', 'encode' => false]])->input('input', ['list' => "users__list", 'placeholder' => 'Введите id, username, имя или фамилию пользователя', 'class' => 'form-control first-deposit-user_id find-user__list', 'disabled' => $model->user_id ? true : false, 'value' => $model->user_id ? $model->user_id : '']) ?>
                    <?= $model->user_id AND $model->id ? 'Пользователь: <a href="/user/' . $model->user_id . '">' . $model->user->getUserForList()['string'] . '</a><hr/>' : null ?>
                    <datalist id="users__list">
                        <?php if ($model->user_id) { ?>
                            <option class="trader" value="<?= $model->user_id ?>"><?= $model->user->getUserForList()['string'] ?></option>
                        <?php } ?>
                    </datalist>
                    <?= $form->field($model, 'comment')->textarea() ?>
                    <?= $form->field($model, 'rating')->dropDownList($marks) ?>
                    <label class="control-label">Дата добавления отзыва</label>
                    <?php echo DatePicker::widget([
                        'model' => $model,
                        'attribute' => 'date_add',
                        'value' => date('Y-m-d', strtotime($model->date_add)),
                        'options' => ['placeholder' => 'Выберите дату создания счета'],
                        'language' => 'ru',
                        'pluginOptions' => [
                            'autoclose' => true,
                            'defaultViewDate' => date('Y-m-d', strtotime($model->date_add)),
                            'toggleActive' => true,
                            'format' => 'yyyy-mm-dd',
                        //    'startDate' => date('Y-m-d', strtotime($solution->date_add)),
                            'endDate' => date('Y-m-d')
                        ]
                    ]); ?>
                    <?= $form->field($model, 'show')->dropDownList([1 => 'Да', 0 => 'Нет']) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Сохранить') ?>
                        <?php if ($model->id) { ?>
                            <a type="button" class="btn btn-danger" href="/user/delete-solution-review/<?= $model->id ?>" data-confirm="Вы действительно хотите удалить этот отзыв?">Удалить</a>
                        <?php } ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>