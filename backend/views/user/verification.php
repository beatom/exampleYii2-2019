<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$this->title = 'Верификация';
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
                    <td>Пользователь верифицирован</td>
                    <td><?= $user->verified ? 'Да' : 'Нет' ?></td>
                    <td><?= $user->verified ? '<a type="button" href="/user/unverificate/' . $user->id . '" class="btn btn-warning">Отменить верификацию</a>' : '<a type="button" href="/user/verificate/' . $user->id . '" class="btn btn-success">Верифицировать</a>' ?></td>
                </tr>
                <tr>
                    <td>Внесено средств</td>
                    <td><?= $user->countInvested() ?>$</td>
                    <td></td>
                </tr>
            </table>

            <div class="row">

                    <div class="col-md-6">
                        <?php if ($documents->pasport_1) { ?>
                        <h3>Основная страница</h3>
                        <a href="<?= $protocol . Yii::$app->params['frontendDomen'] . $documents->pasport_1 ?>" target="_blank">
                            <img style="max-height: 300px; width: auto;" src="<?= $protocol . Yii::$app->params['frontendDomen'] . $documents->pasport_1 ?>">
                        </a>
                        <?php } else {
                            echo 'Основная страница не загружена </br>';
                        } ?>
                    </div>

                <div class="col-md-6">
                    <?php if ($documents->pasport_2) { ?>
                    <h3>Страница с пропиской</h3>
                    <a href="<?= $protocol . Yii::$app->params['frontendDomen'] . $documents->pasport_2 ?>" target="_blank">
                        <img style="max-height: 300px; width: auto;" src="<?= $protocol . Yii::$app->params['frontendDomen'] . $documents->pasport_2 ?>">
                    </a>
                    <?php } else {
                        echo 'Страница с пропиской не загружена </br>';
                    } ?>
                </div>
            </div>

            <?php if ($documents->need_verification) { ?>
                <div class="form-group" style="margin-top:30px;">
                    <a class="btn btn-primary" href="<?= Url::to('/user/verificate/' . $user->id) ?>">Подтвердить</a>
                    <a class="btn btn-warning" href="<?= Url::to('/user/decline_documents/' . $user->id) ?>"><?= $user->verified ? 'Скрыть' : 'Отказать' ?></a>
                </div>
            <?php } ?>

            <hr>
            <h4>Документы загруженные пользователем</h4>
            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Дата зугрузки</th>
                    <th>Изображение</th>
                </tr>
                <tbody>
                <?php foreach ($uploaded as $upload) {
                    ?>
                    <tr>
                        <td><?= $upload->id ?></td>
                        <td><?= $upload->date_add ?></td>
                        <td>

                            <a href="<?= $protocol . Yii::$app->params['frontendDomen'] . $upload->file ?>" target="_blank">
                                <img style="max-height: 50px; width: auto;" src="<?= $upload->base_image ? $upload->base_image : $protocol . Yii::$app->params['frontendDomen'] . $upload->file ?>">
                            </a>
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

