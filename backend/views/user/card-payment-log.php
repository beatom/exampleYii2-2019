<?php

use yii\widgets\LinkPager;
use common\models\PaymentCardRequest;

$this->title = 'Заявки пополнение баланса переводом на карту';

?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">

        <table class="table">

            <tr>
                <th>id</th>
                <th>Пользователь</th>
                <th>Дата</th>
                <th>Номер карты</th>
                <th>Сумма руб.</th>
                <th>Сума $</th>
                <th>Статус</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                    <td> <?= $model->id ?></td>
                    <td><a href="/user/<?= $model->user->id ?>"><?= $model->user->username.'( id:'.$model->user->id.')' ?></a></td>
                    <td><?= $model->date_add ?> </td>
                    <td><?= $model->card_number ?></td>
                    <td><?= $model->summ_rub ?> руб.</td>
                    <td><?= $model->summ_usd ?> $</td>
                    <td><?= PaymentCardRequest::status_name[$model->status] ?></td>
                    <td>
                        <?php if ($model->status == 1) { ?>
                            <a type="button" href="/user/aprovecardpayment/<?= $model->id ?>" class="btn btn-success">Подтвердить</a>
                            <a type="button" href="/user/declinecardpayment/<?= $model->id ?>" class="btn btn-warning">Отклонить</a>
                        <?php } ?>
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