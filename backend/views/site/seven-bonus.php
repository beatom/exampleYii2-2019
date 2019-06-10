<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\BalanceLog;
use yii\helpers\Url;
use common\service\Servis;
use common\models\User;

$this->title = 'Заявки на получение бонуса +7%';
$service = Servis::getInstance();
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">
        <table class="table">
            <tr>
                <th>Id</th>
                <th>Пользователь</th>
                <th>Вк</th>
                <th>Instagram</th>
                <th>Баланс</th>
                <th>Дата</th>
                <th></th>
            </tr>
            <tbody>
            <?php foreach ($models as $model) {
                ?>
                <tr>
                <td> <?= $model->id ?></td>
                <td> <a href="/user/<?= $model->user_id ?>"><?= $model->user->username . ' (id='. $model->user_id . ')' ?></a> </td>
                <td> <?= $model->vk ?> <a href="<?= strripos($model->vk, 'vk.com') ? $model->vk : 'https://vk.com/'.$model->vk ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a></td>
                <td> <?= $model->instagram ?>  <a href="<?= strripos($model->instagram, 'instagram.com') ? $model->instagram : 'https://instagram.com/'.$model->instagram ?>" target="_blank"><span class="glyphicon glyphicon-new-window"></span></a></td>
                <td> <?= $service->beautyDecimal($model->user->balance, 2)  ?>$</td>
                <td> <?= $model->date_add ?></td>
                <td><?= $model->status == 1 ? '<a class="btn btn-success" href="/site/seven-bonus-approve/'.$model->id.'">Начислить бонус</a> <a class="btn btn-warning" href="/site/seven-bonus-decline/'.$model->id.'">Отказать</a>' : \common\models\UserBonusRequest::$statuses[$model->status]  ?></td>
                </tr><?php
            }?>
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