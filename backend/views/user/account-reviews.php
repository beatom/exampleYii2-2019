<?php
use common\models\trade\TradingAccount;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\service\Servis;

$this->title = 'Отзывы об управляющих';
$service = Servis::getInstance();
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
                <div class="form-group">
                    <a class="btn btn-primary" href="<?= Url::to('/user/add-account-review/'.$user->id) ?>">Добавить отзыв</a>
                </div>
                <tr>
                    <th>id</th>
                    <th>Счет ДУ</th>
                    <th>Дата</th>
                    <th>Оценка</th>
                    <th>Ответ</th>
                    <th>Отображается</th>
                    <th></th>
                </tr>
                <tbody>
                <?php foreach ($models as $model) { ?>
                    <tr>
                        <td><?= $model->id ?></td>
                        <td><a href="/trade/account/<?= $model->trading_account_id ?>"><?= $model->account->name ?></a></td>
                        <td> <?= date('d-m-Y', strtotime($model->date_add)) ?></td>
                        <td><?= $model->rating ?></td>
                        <td><?= $model->answer ? '+' : '-' ?></td>
                        <td><?= $model->show ? '+' : '-' ?></td>

                        <td>
                            <a type="button" class="btn btn-info" href="/user/edit-account-review/<?= $model->id ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                            <a type="button" class="btn btn-danger" href="/user/delete-account-review/<?= $model->id ?>" data-confirm="Вы действительно хотите удалить отзыв id:<?= $model->id ?>?"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
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