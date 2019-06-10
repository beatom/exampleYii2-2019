<?php

// подключаем виджет постраничной разбивки
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = 'Карточки менеджеров';
?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

    <h1><?= $this->title ?></h1>


    <div class="body-content">

        <table class="table">
            <div class="form-group">
                <a class="btn btn-primary" href="<?= Url::to('/seting/add-manager-card') ?>">Добавить карточку менеджера</a>
            </div>
            <tr>
                <th>id</th>
                <th>Имя</th>
                <th>Телефон</th>
                <th>E-mail</th>
                <th>Менеджер по умолчанию</th>
                <th></th>
            </tr>
            <tbody>
            <?php foreach ($models as $model) { ?>
                <tr>
                    <td><?= $model->id ?></td>
                    <td><?= $model->name ?></td>
                    <td><?= $model->phone ?></td>
                    <td><?= $model->email ?></td>
                    <td><a type="button" href="/seting/toggle_manager_card/<?= $model->id ?>" class="btn <?= $model->is_main ? 'btn-warning' : 'btn-info'?>"><?= $model->is_main ? 'Деактивировать' : 'Сделать основным'?></a></td>
                    <td>
                        <a type="button" href="/seting/manager-card/<?= $model->id ?>" class="btn btn-success">Редактировать</a>
                        <a type="button" href="/seting/delete-manager-card/<?= $model->id ?>" data-confirm="Вы дествительно хотите удалить карточку менеджера?" class="btn btn-danger">Удалить</a>
                    </td>

                </tr>

                <?php
            } ?>
            </tbody>
        </table>
    </div>
</div>
