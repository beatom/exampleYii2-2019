<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Логи партнерских счетов';

$status = (isset($_GET['status']))?$_GET['status']:0;
$operation = (isset($_GET['operation']))?$_GET['operation']:0
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">

        <div class="form-group">
            <p>Итог по результату</p>
            начислено: <?= $query_info_in ?>$
            <br>
            использовано: <?= $query_info_out ?>$
        </div>

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>

            <?= Html::textInput('user', ( (isset($_GET['user']))? $_GET['user']: '' ),['placeholder'=>'пользователь']) ?>

            с <input type="date" name="date_to" value="<?= (isset($_GET['date_to']))? $_GET['date_to']: '' ?>">
            до <input type="date" name="date_from" value="<?= (isset($_GET['date_from']))? $_GET['date_from']: '' ?>">

            <?= Html::submitButton('Выбрать', ['class'=>'btn btn-primary']) ?>

            <?= Html::endForm()?>

        </div>

        <table class="table">

            <tr>
                <th>id</th>
                <th>Дата</th>
                <th>Пользователь</th>
                <th>Сумма</th>
                <th>От</th>
                <th>Доход</th>
                <th>Комментарий</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                <td> <?= $model->id ?></td>
                <td> <?= $model->date_add ?> </td>
                <td> <?php if(!$model->user) { echo '-';} else { echo '<a href="'.Url::to('/user/'.$model->user->id).'">'.$model->user->username.' ( '.$model->user->id.' )</a>';} ?></td>
                <td> <?= number_format($model->summ, 4, '.', '') ?></td>
                <td> <?php if(!$model->from_user_id ) { echo '-';} else { echo '<a href="'.Url::to('/user/'.$model->from_user_id).'">'.$model->partner->username.' ( '.$model->from_user_id.' )</a>';} ?></td>
                <td> <?= $model->description ?></td>
                <td> <?php print_r($model->comment) ?></td>

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