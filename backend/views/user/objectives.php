<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;


$this->title = 'Цели пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-signup">
    <h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
    <div class="row">
        <div class="col-lg-2">
            <?= $this->render('menu', ['user_id' => $user->id]) ?>
        </div>

        <div class="col-lg-10">
            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Сумма цели</th>
                    <th>Баланс на старте</th>
                    <th>Дата добавления</th>
                    <th>Дата выполнения</th>
                    <th>Комментарий</th>
                    <th>Изображение</th>
                </tr>
                <tbody class="find-accounts-all__list">
                <?php foreach ($models as $model) {
                    ?>
                    <tr>
                        <td><?= $model->id ?></td>
                        <td><?= $model->sum_end ?>$</td>
                        <td><?= $model->sum_start ?>$</td>
                        <td><?= $model->date_add ?></td>
                        <td><?= $model->date_end ? $model->date_end : '<b>Текуща цель</b>'  ?></td>
                        <td><?= $model->comment ?> </td>
                        <td style="text-align: center;">
                            <?= $model->image ?
                                '<a href="//'.Yii::$app->params['frontendDomen'] . $model->image.'" target="_blank"><img src="//'.Yii::$app->params['frontendDomen'].$model->image.'" style="max-width: 25px;"></a>' : 
                                '-'
                            ?>
                        </td>
                    </tr>

                    <?php
                } ?>
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
</div>

