<?php
use common\models\trade\TradingAccount;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\service\Servis;

$this->title = 'Торговые счета ' . $user->username;
$service = Servis::getInstance();
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
<div class="row">
    <div class="col-lg-2">
        <?= $this->render('menu', ['user_id' => $user->id]) ?>
    </div>
    <div class="col-lg-10">
        <table class="table">
            <tr>
                <th>id</th>
                <th>Название</th>
                <th>Дата открытия</th>
                <th>Тип счета</th>
                <th>Счет ДУ</th>
                <th>Прибыль</th>

                <th></th>
            </tr>
            <tbody>
            <?php foreach ($accounts as $account) { ?>
                <tr>
                    <td><?= $account->id ?></td>
                    <td><a href="/trade/account/<?= $account->id ?>"><?= $account->name ?></a></td>
                    <td> <?= $account->date_add ?></td>
                    <td><?= TradingAccount::type_name[$account->type_account] ?></td>
                    <td><?= $account->is_du ? '+' : '-' ?></td>
                    <td><?= $service->beautyDecimal($account->profit, 5) ?>%</td>

                    <td><a href="/trade/account/<?= $account->id ?>">Подробнее</a></td>
                </tr>

                <?php
            } ?>
            </tbody>
        </table>

        <?php
        // отображаем постраничную разбивку
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>
    </div>
</div>