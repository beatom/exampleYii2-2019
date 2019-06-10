<?php

use yii\helpers\Html;
use yii\helpers\Url;

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$this->title = 'Овердрафт';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-signup">
    <h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
    <div class="row">
        <div class="col-lg-2">
            <?= $this->render('menu', ['user_id' => $user->id]) ?>
        </div>

        <div class="col-lg-10">
            <?php
            if (!$overdraft) {
                echo '<div class="form-group">У пользователя нет открытого Овердрафита</div>';
            } else { ?>

                <?= Html::beginForm('#', 'post', ['name' => 'changeOverdraft']) ?>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="control-label">Сумма долга</label>
                        <div class="input-group"><span class="alert-success input-group-addon">$</span>
                            <?= Html::input('text', 'summ', $overdraft->summ, ['class' => 'form-control']) ?>
                        </div>
                        <div class="help-block"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Размер овердрафта</label>
                        <div class="input-group"><span class="alert-success input-group-addon">$</span>
                            <?= Html::input('text', 'sdfgsdfgerytrty', $overdraft->full_summ, ['class' => 'form-control', 'disabled' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="control-label">Баланс пользователя с овердрафтом</label>
                        <div class="input-group">
                            <span class="alert-success input-group-addon">$</span>
                            <input class="form-control" value="<?= $overdraft->user_balance ?>" disabled="" type="text"></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Процент овердрафта от баланса пользователя</label>
                        <div class="input-group">
                            <span class="alert-success input-group-addon">%</span>
                            <input class="form-control" value="<?= $overdraft->percent ?>" disabled="" type="text"></div>
                    </div>
                </div>


                <div class="form-group">
                    <label class="control-label">Дата закрытия</label>
                    <?= Html::input('date', 'date_close', $overdraft->date_close, ['class' => 'form-control']) ?>
                </div>

                <div class="form-group">
                    <label class="control-label" for="user-balance">Комментарий</label>
                    <?= Html::input('text', 'comment', '', ['class' => 'form-control']) ?>
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Сохранить', [
                        'class' => 'btn btn-primary',
                    ]) ?>
                    <a href="<?= Url::to('/site/money_log?user=' . $user->username) ?>" target="_blank">История</a>
                </div>

                <?= Html::endForm() ?>


                <a type="button" href="/trade/runoverdraft/<?= $overdraft->id ?>" class="btn btn-warning">Запустить овердрафт</a>
                <div class="box">
                    <h4>Расчет списания средств во время закрытия овердрафта</h4>
                    <?= $overdraft->end_comment ? $overdraft->end_comment : $overdraft->rolloverWithComments() ?>
                </div>
                <div class="box">
                    <h4>Расчет баланса пользоватля на начало овердрафта</h4>
                    <?= $overdraft->start_comment ?>
                </div>
                <?php
            }
            ?>


        </div>
    </div>
</div>

