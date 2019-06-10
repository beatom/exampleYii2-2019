<?php

use yii\widgets\LinkPager;
use common\models\PaymentCardRequest;

$this->title = 'Овердрафты';

?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">

        <table class="table">

            <tr>
                <th>id</th>
                <th>Пользователь</th>
                <th>Сумма</th>
                <th>Текущий долг</th>
                <th>Дата открытия</th>
                <th>Дата окончания</th>
                <th>Активный</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                    <td> <?= $model->id ?></td>
                    <td><a href="/user/<?= $model->user->id ?>"><?= $model->user->username.'( id:'.$model->user->id.')' ?></a></td>
                    <td><?= $model->full_summ ?> </td>
                    <td><?= $model->summ ?></td>
                    <td><?= $model->date_open ?></td>
                    <td><?= $model->date_close ?></td>
                    <td><?= $model->is_dolg ? "Да" : '-' ?></td>
                    <td>
                            <a  href="/user/overdraft/<?= $model->id ?>" >Подробнее</a>
                </tr>
                <?php
            } ?>

        </table>

        <?php
        // отображаем постраничную разбивку
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);

        ?>

    </div>
</div>