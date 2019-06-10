<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\BalanceLog;
use common\models\User;
use common\models\trade\Investment;
use common\models\Events;

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$this->title = $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = $this->title;

$service = \common\service\Servis::getInstance();

$withdraw1 = BalanceLog::find()->where(['user_id' => $user->id, 'operation' => 1, 'status' => 1])->sum('summ');
$withdraw2 = BalanceLog::find()->where(['user_id' => $user->id, 'operation' => 1, 'status' => [0, 3]])->sum('summ');
$withdraw1 = $withdraw1 ? $service->beautyDecimal($withdraw1) : 0;
$withdraw2 = $withdraw2 ? $service->beautyDecimal($withdraw2) : 0;


$user_info['inv_balance'] = 0;
$user_info['inv_earned'] = 0;


$user_info['inv_earned_show'] = 0;
$user_info['inv_earned'] = 0;

$objective = $user->getActiveObjective();

$current_using_summ = $user->balance * (Events::getCurrentBankPercent() / 100);
$total_profit = $user->getProfit(); //DaysLog::getPeriod()->getCurrentProfit()
$last_day_result = BalanceLog::find()
    ->where(['user_id' => $user->id, 'status' => 1, 'operation' => 5])
    ->andWhere('date_add BETWEEN "'.date('Y-m-d 10:00:00', strtotime(' -1 day')).'" AND "'.date('Y-m-d 15:00:00').'"')
    ->sum('summ');

?>
<div class="site-signup">
    <h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
    <div class="row">
        <div class="col-lg-2">
            <?= $this->render('menu', ['user_id' => $user->id]) ?>
        </div>
        <div class="col-lg-10">
            <table class="table">
                <tr>
                    <td width="30%">Ник</td>
                    <td><?= $user->username ?></td>
                </tr>
                <tr>
                    <td>ФИО</td>
                    <td><?= $user->firstname ?> <?= $user->lastname ?> <?= $user->middlename ?></td>
                </tr>
                <tr>
                    <td>День рождения</td>
                    <td><?php echo date('d-m-Y', strtotime($user->date_bithday)) ?></td>
                </tr>
                <tr>
                    <td>E-mail</td>
                    <td><?= $user->email ?></td>
                </tr>
                <tr>
                    <td>Телефон</td>
                    <td><?= $user->phone ?></td>
                </tr>
                <tr>
                    <td>Дата регистрации</td>
                    <td><?php echo date('d-m-Y', strtotime($user->date_reg)) ?></td>
                </tr>
                <tr>
                    <td>Пользователь верифицирован</td>
                    <td><?= $user->verified ? 'Да' : 'Нет' ?></td>
                </tr>
                <tr>
                    <td>Изменить пароль</td>
                    <td>
                        <?= Html::beginForm('#', 'post'); ?>

                        <?= Html::input('text', 'pass', null, ['class' => 'form-control', 'style' => 'width: 50%;display: inline;']); ?>
                        <?= Html::input('hidden', 'action', 'changepass'); ?>
                        <?= Html::submitButton('Изменить', ['class' => 'btn btn-primary']); ?>

                        <?= Html::endForm(); ?>

                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                </tr>
                <tr>
                    <td>Баланс пользоваетля</td>
                    <td><b><?= $service->beautyDecimal($user->balance) ?></b></td>
                </tr>
                <tr>
                    <td>Партнерский счет, сумма</td>
                    <td><b><?= $service->beautyDecimal($user->balance_partner) ?></b></td>
                </tr>
                <tr>
                    <td>Пополнил</td>
                    <td><b><?= $service->beautyDecimal(BalanceLog::find()->where(['user_id' => $user->id, 'status' => 1, 'operation' => [0, 2], 'system' => BalanceLog::$user_min_bal_systems])->andWhere('summ > 0')->sum('summ')) ?></b></td>
                </tr>
                <tr>
                    <td>Вывел <?= $withdraw1 != $withdraw2 ? '(Выполнено / В обработке)' : null ?></td>
                    <td><b><?= $withdraw1 == $withdraw2 ? $withdraw1 : $withdraw1 . ' / ' . $withdraw2 ?></b></td>
                </tr>

                <tr>
                    <td>Заработано</td>
                    <td><b><?= $service->beautyDecimal($total_profit) ?></b></td>
                </tr>

                <tr rowspan="2">
                    <td colspan="2"></td>
                </tr>
                <tr rowspan="2">
                    <td colspan="2" style="border-top: none">
                        <h3>Текущая цель пользователя</h3>
                    </td>
                </tr>

                <?php
                if ($objective) {
                    ?>
                    <tr>
                        <td>Комментарий</td>
                        <td><?= $objective->comment ?></td>
                    </tr>
                    <tr>
                        <td>Начальная сумма / желаемая</td>
                        <td><?= $objective->sum_start . ' / ' . $objective->sum_end . ' ( ' . $objective->percent . '% )' ?></td>
                    </tr>
                    <tr>
                        <td>Дата добавления</td>
                        <td><?= $objective->date_add ?></td>
                    </tr>
                    <?php if ($objective->data) { ?>
                        <tr>
                            <td>Отображаемый текст</td>
                            <td>
                                <b><?= $objective->data->title ?></b>
                                <br>
                                <?= $objective->data->description ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Изображение</td>
                        <td><a href="//<?= Yii::$app->params['frontendDomen'] . $objective->image ?>" target="_blank"><img src="//<?= Yii::$app->params['frontendDomen'] . $objective->image ?>" style="max-width: 100px;"></a></td>
                    </tr>


                <?php } else { ?>
                    <tr>
                        <td colspan="2"> У пользователя нет активной цели</td>
                    </tr>

                <?php } ?>
            </table>
            <hr>
        </div>
        <?php if (!$user->banned) { ?>
            <a type="button" href="/user/ban_user/<?= $user->id ?>" class="btn btn-danger">Забанить</a>
        <?php } else { ?>
            <a type="button" href="/user/unban_user/<?= $user->id ?>" class="btn btn-danger">Разбанить</a>
        <?php } ?>
    </div>

</div>

