<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\models\trade\RequestLeverage;

$this->title = 'Бонусы и Балы';
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

            <div class="form-group">
                Партнерский счет пользователя <b><?= $user->balance_partner ?></b> $
                <br>Балы invest пользователя <b><?= $user->ball_invest ?></b> C
                <br>На бонусном счету пользователя  <b><?= \common\service\Servis::getInstance()->beautyDecimal($user->getSummBonuses()); ?></b> $
            </div>

            <h3>Партнерский счет</h3>
            <?= Html::beginForm('#', 'post', ['name' => 'setBalanseBonusUser']) ?>

            <div class="form-group">
                <label class="control-label" for="user-balance">Пополнить партнерский счет на. Если указать отрицательное число то сумма будет списана</label>
                <div class="input-group"><span class="alert-success input-group-addon">$</span>
                    <?= Html::input('text', 'balance-bonus', '', ['class' => 'form-control']) ?>
                </div>
                <div class="help-block"></div>
            </div>

            <div class="form-group">
                <label class="control-label" for="user-balance">Комментарий</label>
                <?= Html::input('text', 'comment-bonus', '', ['class' => 'form-control']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('отправить', ['class' => 'btn btn-primary', 'name' => 'set-balanse-bonus']) ?>
                <a href="<?= Url::to('/site/partner-log?user=' . $user->username) ?>">история</a>
            </div>

            <?= Html::endForm() ?>


            <h3>Балы</h3>
            <?= Html::beginForm('#', 'post', ['name' => 'setBalanseBallUser']) ?>

            <div class="form-group">
                <label class="control-label" for="user-balance">Пополнить балы на. Если указать отрицательное число то сумма будет списана</label>
                <div class="input-group"><span class="alert-success input-group-addon">$</span>
                    <?= Html::input('text', 'balance-ball', '', ['class' => 'form-control']) ?>
                </div>
                <div class="help-block"></div>
            </div>

            <div class="form-group">
                <label class="control-label" for="user-balance">Комментарий</label>
                <?= Html::input('text', 'comment-ball', '', ['class' => 'form-control']) ?>
            </div>

            <div class="form-group">
                <?= Html::submitButton('отправить', ['class' => 'btn btn-primary', 'name' => 'set-balanse-ball']) ?>
                <a href="<?= Url::to('/site/ball-log?user=' . $user->username) ?>">история</a>
            </div>

            <?= Html::endForm() ?>

            <h3>Бонусы</h3>

            <div class="form-group">
                Бонусные счета создаются и редактируются в <a href="<?= Url::to('/site/bonus-log?user=' . $user->username) ?>">истории</a> из-за своей специфики
            </div>


            <h3>Невыплаченные бонусы</h3>
            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Сумма</th>
                    <th>Дата начисления</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
                <tbody>
                <?php
                if ($bonus_debts) { ?>
                    <?php foreach ($bonus_debts as $debt) { ?>
                        <tr>
                            <td><?= $debt->id ?></td>
                            <td><?= number_format($debt->summ, 2) ?>$</td>
                            <td> <?= $debt->date_add ?></td>
                            <td><?= RequestLeverage::status_name[$debt->status] ?></td>
                            <td><?= $debt->status == 1 ? '<a href="/user/remove-bonus-debt/' . $debt->id . '">Отменить</a>' : null ?></td>
                        </tr>
                    <?php }
                    } else {
                    echo '<tr><td colspan="5" style="text-align: center">Нет записей</td></tr>';
                } ?>
                </tbody>
            </table>
        </div>


    </div>


</div>

