<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = 'Логи смс';
?>

<div class="site-index">

        <h1><?= $this->title ?></h1>

    <div class="body-content">

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>

            <?= Html::textInput('user', ( (isset($_GET['user']))? $_GET['user']: '' ),['placeholder'=>'пользователь']) ?>
            <?= Html::textInput('phone', ( (isset($_GET['phone']))? $_GET['phone']: '' ),['placeholder'=>'телефон']) ?>

            <?= Html::submitButton('Выбрать', ['class'=>'btn btn-primary']) ?>

            <?= Html::endForm()?>

        </div>

        <table class="table">

            <tr>
                <th>id</th>
                <th>Пользователь</th>
                <th>Телефон</th>
                <th>Сообщение</th>
                <th>Дата и время</th>
                <th>Статус</th>
                <th>Смс провайдер</th>
                <th>smscId</th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                <td> <?= $model->id ?></td>
                <td> <?php if(!$model->user_id) { echo '-';} else { echo $model->user->username;} ?></td>
                <td> <?= $model->phone ?></td>
                <td> <?= $model->text ?> </td>
                <td> <?= $model->date_add ?> </td>
                <td> <?= $model->status ?></td>
                <td> <?= $model->manager->name ?></td>
                <td> <?= $model->smscId ?> </td>
                
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