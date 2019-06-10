<?php
use common\models\trade\TradingAccount;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\service\Servis;

$this->title = 'Инвестиции ' . $user->username;
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
                <th>Тип</th>
                <th>Дата инвестирования</th>
                <th>Бонусы</th>
                <th>Вложено</th>
                <th>Текущая сумма</th>
                <th>Прибыль</th>
                <th>Закрыта</th>
                <th></th>
            </tr>
            <tbody class="find-accounts-all__list">
            <?php foreach ($investments as $investment) {
                $user = $investment->user;
                $invested = $investment->countInvested();
                $invested = $invested ? $invested : 0;
                $target = '';
                if($investment->trading_account_id) {
                    $target = '<a href="/trade/account/'.$investment->trading_account_id.'">' .$investment->account->name.'</a>';
                    if($investment->account->type_account == 4) {
                        $target .= ' <span style="color: grey;"> (демо)</span>';
                    } elseif ($investment->account->type_account == 5) {
                        $target .= ' <span style="color: grey;"> (cent)</span>';
                    }
                } elseif($investment->solution_id) {
                    $target = '<a href="/trade/solution/'.$investment->solution_id.'">' .$investment->solution->name.'</a>';
                }
                ?>
                <tr>
                    <td><?= $investment->id ?></td>
                    <td><?= $target ?></td>
                    <td><?= $investment->date_add ?></td>
                    <td><?= $investment->bonus_money ? '<a href="/site/bonus-add/'.$investment->bonus_money.'">'.$investment->bonus_money.'</a>' : '-' ?></td>
                    <td><?= $invested ?> $</td>
                    <td><?= $service->beautyDecimal($investment->summ_current + $investment->investedToday()) ?> $</td>
                    <td><?= $service->beautyProfit($investment->profit, false) ?> %</td>
                    <td><?= $investment->deleted ? 'Закрыта' : '-' ?></td>
                    <td><a href="/trade/investment/<?= $investment->id ?>">Подробнее</td>
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