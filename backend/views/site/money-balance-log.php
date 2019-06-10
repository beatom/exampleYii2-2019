<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\BalanceLog;
use yii\helpers\Url;
use common\service\Servis;
use common\models\User;

$this->title = 'Баланс пользователей';
$service = Servis::getInstance();
$order = (isset($_GET['order']))? $_GET['order'] : 'id';
$manger_id = (isset($_GET['manager_id']))? $_GET['manager_id'] : 0;
$user = (isset($_GET['user']))? $_GET['user'] : '';
$order_type = (isset($_GET['order_type']))? $_GET['order_type']:'ASC';
$order_types = [
    'ASC' => 'По возростанию',
    'DESC' => 'По убыванию',
];

//foreach ($_GET as $key => $value) {
//    unset($_GET[$key]);
//}
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>
            Сортировать по:

            <?= Html::input('text', 'user', $user, [ 'placeholder' => 'пользователь' ]) ?>
            <?= Html::dropDownList('order',[], User::$balance_sort, ['options' => [$order => ['selected' => 'selected' ]]]) ?>
            <?= Html::dropDownList('order_type',[], $order_types, ['options' => [$order_type => ['selected' => 'selected' ]]]) ?>
            <?= Html::dropDownList('manager_id',[], $managers, ['options' => [$manger_id => ['selected' => 'selected' ]]]) ?>
            <?= Html::submitButton('Выбрать', ['class'=>'btn btn-primary']) ?>
            <button type="submit" class="btn btn-warning" name="export">Экспорт</button>
            <?= Html::endForm()?>

        </div>

        <table class="table">
            <tr>
                <th>Id</th>
                <th>Логин</th>
                <th>Баланс</th>
                <th>Баланс инвестиций</th>
                <th>Сумма</th>
                <th>Партнерский счет</th>
                <th>Сумма с партнерским счетом</th>
                <th>Дата первого пополнения</th>
                <th>Первый ввод</th>
                <th>Вводы</th>
                <th>Выводы</th>
                <th>Разница</th>
                <th>Результат</th>
                <th>Заработано</th>
            </tr>
            <tbody>
            <?php foreach ($models as $model) { ?>
                <tr>
                <td> <?= $model->id ?></td>
                <td> <a href="/user/<?= $model->id ?>"><?= $model->username ?></a> </td>
                <td> <?= $service->beautyDecimal($model->balance, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->investments_summ, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->total_b, 2, ',')  ?></td>
                <td> <?= $service->beautyDecimal($model->balance_partner, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->total_b_with_p, 2, ',') ?></td>

                <td> <?= $model->first_deposit_date ?></td>
                <td> <?= $service->beautyDecimal($model->first_deposit_sum, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->deposit_sum, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->withdraw_sum, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->difference, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->result, 2, ',') ?></td>
                <td> <?= $service->beautyDecimal($model->dtp, 2, ',')  ?></td>
                </tr><?php
            }?>
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