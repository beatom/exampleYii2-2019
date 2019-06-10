<?php
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <h1>Административная панель invest24</h1>

    <ul>
        <?= Yii::$app->user->can('manager') ? ' <li><a href="/money_log">Движения средств</a></li>' : null ?>
        <?= Yii::$app->user->can('moderator') ? ' <li><a href="chat/index">Чат</a></li>' : null ?>
    </ul>



    <?php
    if (Yii::$app->user->can('admin')) { ?>
    <h3 style="margin-top: 50px">Авторизированные пользователи</h3>
    <style>
        table.table td {
            border: 1px solid gainsboro !important;
        }
    </style>
    <table class="table" style="width: auto;text-align: center">
        <thead>
        <tr>
            <td>Пользователь</td>
            <td>ip</td>
            <td>Время последней активности</td>
        </tr>
        </thead>
        <tbody>
        <?php
        $active_sessions = \common\models\UserIpLogAdmin::getLastLogs();
        if (!empty($active_sessions['non_admin'])) { ?>
            <?php foreach ($active_sessions['non_admin'] as $as) { ?>
                <tr>
                    <td><?= '<a href="' . Url::to('/user/' . $as->user->id) . '">' . $as->user->username . ' ( ' . $as->user->id . ' )</a>' ?></td>
                    <td><?= $as->ip ?></td>
                    <td><?= $as->date_add ?></td>
                </tr>
            <?php } ?>
        <?php }
        if (!empty($active_sessions['admin'])) { ?>
            <tr>
                <td colspan="3">Администратор</td>
            </tr>
            <?php foreach ($active_sessions['admin'] as $as) { ?>
                <tr>
                    <td><?= '<a href="' . Url::to('/user/' . $as->user->id) . '">' . $as->user->username . ' ( ' . $as->user->id . ' )</a>' ?></td>
                    <td><?= $as->ip ?></td>
                    <td><?= $as->date_add ?></td>
                </tr>
            <?php } ?>
        <?php } ?>

        </tbody>
        <?php } ?>

    </table>
</div>
</div>
