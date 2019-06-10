<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use common\models\trade\TradingAccount;

$this->title = 'Инвестирование бонуса';
$this->params['breadcrumbs'][] = ['label' => 'Бонусы', 'url' => ['/site/bonus-log']];
$this->params['breadcrumbs'][] = ['label' => 'Бонус ' . $bonus->id, 'url' => ['/site/bonus-add/' . $bonus->id]];
$this->params['breadcrumbs'][] = $this->title;


$accounts = TradingAccount::find()->where(['is_du' => 1, 'is_active' => 1])->andWhere('user_id <>'.$bonus->user_id)->orderBy('name')->all();
$trading_accounts = [];
$first_account = $model->trading_account_id ? $model->trading_account_id : false;
foreach ($accounts as $a) {
    $trading_accounts[$a->id] = 'id:' . $a->id . ', ' . $a->name;
}

?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-invest-bonus']); ?>


            <?= $form->field($model, 'summ')->input('text', ['placeholder' => $bonus->summ_now, 'value' => $bonus->summ_now ? $model->summ : 0])->label('Сумма инвестирования') ?>
            <?= $form->field($model, 'trading_account_id')->dropDownList($trading_accounts, ['class' => 'form-control select__account'])->label('Счет ДУ') ?>


            <div class="form-group">
                <?= Html::submitButton('Инвестировать', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
