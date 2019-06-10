<?php
use common\models\trade\TradingAccount;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\service\Servis;

$this->title = 'Сообщения пользователя' . $user->username;
$service = Servis::getInstance();
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
<div class="row">

    <div class="col-lg-2">
        <?= $this->render('menu', ['user_id' => $user->id]) ?>
    </div>
    <div class="col-lg-10">
        <div class="form-group">
            <a class="btn btn-primary" href="<?= Url::to('/user/message_add/'.$user->id) ?>">Отправить сообщение</a>
        </div>
        <table class="table">
            <tr>
                <th>id</th>
                <th>Дата</th>
                <th>Отправитель</th>
                <th>Тема</th>
                <th>Прочитано</th>
                <th></th>
                <th></th>
            </tr>
            <tbody class="find-accounts-all__list">
            <?php foreach ($models as $model) { ?>
                <tr>
                    <td><?= $model->id ?></td>
                    <td><?= $model->date_add ?></td>
                    <td><?= $model->sender->name . ' ' . $model->sender->surname ?></td>

                    <td><?= $model->title ?></td>
                    <td><?= $model->status ? '+' : '-' ?></td>
                    <td><a href="/user/message/<?= $model->id ?>">Редактировать</td>
                    <td><a href="/user/message_delete/<?= $model->id ?>" data-confirm="Вы дейтвительно хотите удалить сообщение?">Удалить</td>
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