<?php

use yii\helpers\Html;
use yii\helpers\Url;

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$this->title = 'Баланс';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-signup">
    <h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
    <div class="row">
        <div class="col-lg-2">
            <?= $this->render('menu', ['user_id'=> $user->id]) ?>
        </div>

        <div class="col-lg-10">

            <div class="form-group">
                Текущий баланс пользователя <b><?= $user->balance ?></b> $
            </div>

            <?= Html::beginForm('#','post',['name'=>'setBalanseUser']) ?>

            <div class="form-group">
                <label class="control-label" for="user-balance">Пополнить на. Если указать отрицательное число то сумма будет списана</label>
                <div class="input-group"><span class="alert-success input-group-addon">$</span>
                    <?= Html::input('text', 'balance', '', ['class'=>'form-control']) ?>
                </div>
                <div class="help-block"></div>
            </div>

            <div class="form-group">
                <label class="control-label" for="user-balance">Комментарий</label>
            <?= Html::input('text', 'comment', '', ['class'=>'form-control']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('Пополнить', ['class' => 'btn btn-primary', 'name' => 'set-balanse-user']) ?>
                <a href="<?= Url::to('/site/money_log?user='.$user->username)?>">история</a>
            </div>

            <?= Html::endForm() ?>
        </div>
    </div>
</div>

