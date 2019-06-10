<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$this->title = 'Овердрафты';
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
            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Сумма овердрафта</th>
                    <th>Текущий долг</th>
                    <th>Дата открытия</th>
                    <th>Дата окончания</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
                <tbody class="find-accounts-all__list">
                <?php foreach ($overdrafts as $overdraft) {
                    ?>
                    <tr>
                        <td><?= $overdraft->id ?></td>
                        <td><?= $overdraft->full_summ ?>$</td>
                        <td><?= $overdraft->summ ?>$</td>
                        <td><?= $overdraft->date_open ?></td>
                        <td><?= $overdraft->date_close ?></td>
                        <td><?= $overdraft->is_dolg ? 'Открыт' : 'Завершен' ?> </td>
                        <td><a href="/user/overdraft/<?= $overdraft->id ?>">Подробнее</td>
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
</div>

