<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Сообщения чата';

$status = (isset($_GET['status']))?$_GET['status']:0;
$operation = (isset($_GET['operation']))?$_GET['operation']:0
?>

<div class="site-index">

    <h3><?= $this->title ?></h3>

    <div class="body-content">

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>

            <?= Html::textInput('user', ( (isset($_GET['user']))? $_GET['user']: '' ),['placeholder'=>'id, ник или email']) ?>

            с <input type="date" name="date_to" value="<?= (isset($_GET['date_to']))? $_GET['date_to']: '' ?>">
            до <input type="date" name="date_from" value="<?= (isset($_GET['date_from']))? $_GET['date_from']: '' ?>">
            текст <input type="text" name="text_like" value="<?= (isset($_GET['text_like']))? $_GET['text_like']: '' ?>">

            <?= Html::submitButton('Выбрать', ['class'=>'btn btn-primary']) ?>
            <a class="btn btn-info" href="/chat/index">Сбросить фильтры</a>
            <?= Html::endForm()?>
            <a class="btn btn-success" href="/chat/add">Добавить сообщение</a>
        </div>

        <table class="table">

            <tr>
                <th>id</th>
                <th>Дата</th>
                <th>Пользователь</th>
                <th>Likes</th>
                <th>Dislikes</th>
                <th>Сообщение</th>
                <th>Ответ на сообщение</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) {

                ?>
                <tr>
                <td> <?= $model->id ?></td>
                <td> <?= $model->date_add ?> </td>
                <td> <?php if(!$model->user) { echo '-';} else {  echo '<a href="'.Url::to('/user/'.$model->user->id).'">'.$model->user->username.' ( '.$model->user->id.' )</a>';} ?></td>
                <td> <?= $model->likes ?></td>
                <td> <?= $model->dislikes ?></td>
                <td> <?= mb_substr(strip_tags($model->text), 0, 255, "UTF-8") ?>...</td>
                <td> <?= $model->parent_id ? '<a href="/chat/edit/'. $model->parent_id . '">' . $model->parent_id . '</a>'  : '-' ?></td>
                <td style="display:inline-flex;">
                    <a href="/chat/edit/<?= $model->id ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="/chat/delete/<?= $model->id ?>" style="margin-left: 5px" data-confirm="Вы действительно хотите удалить это сообщение и все последующие ответы на него?"><span style="color: red" class="glyphicon glyphicon-trash"></span></a>
                </td>
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