<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = 'Шаблоны СМС';
?>

<div class="site-index">

        <h1><?= $this->title ?></h1>

    <div class="body-content">

        <table class="table">

            <tr>
                <th>id</th>
                <th>Название</th>
                <th>текст</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                <td><?= $model->id ?></td>
                <td> <?= $model->synonym ?></td>
                <td> <?= $model->text ?></td>
                <td> <a href="<?= Url::to(['/site/edit_sms', 'id' => $model->id ]) ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
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
