<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\VisitorLog;
//['value' => ($user->firstname)? $user->firstname: '']
$this->title = 'Записи на бизнес встречу';
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">

        <table class="table">

            <tr>
                <th>id</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>Дата добавления заявки</th>
                <th>Желаемая дата посещения</th>
                <th>Город</th>
                <th>Статус</th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                <td> <?= $model->id ?></td>
                <td> <?= $model->name ?></td>
                <td> <?= $model->phone ?></td>
                <td> <?= $model->date_add ?> </td>
                <td> <?= date('m.d.Y', strtotime($model->date_visit)) ?> </td>
                <td> <?= VisitorLog::cities[$model->city_id] ?></td>
                <td> <?= $model->status ? 'Обработано' : '<a class="btn btn-success" href="/site/confirm_visit/'.$model->id.'">Заявка обработана</a>' ?></td>


                </tr><?php
            }?>

        </table>

        <?php
        // отображаем постраничную разбивку
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);

        ?>

    </div>
</div>