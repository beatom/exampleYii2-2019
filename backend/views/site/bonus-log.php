<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\BalanceLog;
use yii\helpers\Url;

$this->title = 'Логи бонусов';

$status = (isset($_GET['status'])) ? $_GET['status'] : 0;
$operation = (isset($_GET['operation'])) ? $_GET['operation'] : 0
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">

        <div class="form-group">
            <p>Итог по результату</p>
            начислено: <?= $query_info_in ?> бонусов
            <br>
            использовано: <?= $query_info_out ?> бонусов
        </div>

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>

            <?= Html::textInput('user', ((isset($_GET['user'])) ? $_GET['user'] : ''), ['placeholder' => 'пользователь']) ?>

            с <input type="date" name="date_to" value="<?= (isset($_GET['date_to'])) ? $_GET['date_to'] : '' ?>">
            до <input type="date" name="date_from" value="<?= (isset($_GET['date_from'])) ? $_GET['date_from'] : '' ?>">

            <?= Html::submitButton('Выбрать', ['class' => 'btn btn-primary']) ?>

            <?= Html::endForm() ?>

        </div>

        <div class="panel-group">

            <?= Html::a('Создать бонусный счет', Url::to('/site/bonus-add'), ['class' => 'btn btn-primary', 'name' => 'add-bonus-log']) ?>

            <?= Html::endForm() ?>

        </div>


        <table class="table">

            <tr>
                <th>id</th>
                <th>Дата</th>
                <th>Дата окончания</th>
                <th>Пользователь</th>
                <th>Сумма</th>
                <th>Сумма сейчас</th>
                <th>действуют, кол-во дней</th>
                <th>Использован</th>
                <th>Действия</th>
                <th>Комментарий</th>
            </tr>

            <?php foreach ($models as $model) { ?>
            <tr>
                <td> <?= $model->id ?></td>
                <td> <?= $model->date_add ?> </td>
                <td> <?= $model->date_end ?> </td>
                <td> <?php if (!$model->user) {
                        echo '-';
                    } else {
                        echo '<a href="' . Url::to('/user/' . $model->user->id) . '">' . $model->user->username . ' ( ' . $model->user->id . ' )</a>';
                    } ?></td>
                <td> <?= $model->summ ?></td>
                <td> <?= $model->summ_now ?></td>
                <td> <?= $model->work_days ?></td>
                <td> <?= $model->expired ? '+' : '-' ?></td>
                <td>
                    <?php
                    if (!$model->expired) {
                    ?>
                    <a href="<?= Url::to('/site/bonus-invest/' . $model->id) ?>"><span class="glyphicon glyphicon glyphicon-usd" title="Инвестировать бонус"></span></a>
                    <?php
                    }
                    ?>

                    <a href="<?= Url::to('/site/bonus-add/' . $model->id) ?>"><span class="glyphicon glyphicon-pencil" title="Редактироовать бонус"></span></a>
                </td>
                <td> <?= $model->description ?></td>
            </tr><?php
            } ?>

        </table>

        <?php
        // отображаем постраничную разбивку
        echo LinkPager::widget(['pagination' => $pages,]);

        ?>

    </div>
</div>