<?php
use yii\widgets\LinkPager;
use common\models\UserIpLog;

$this->title = 'Ip входов на сайт ';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = ['label' => $user->username, 'url' => ['/user/' . $user->id]];
$this->params['breadcrumbs'][] = $this->title;

$ips = UserIpLog::find()->select('ip')->where(['user_id' => $user->id])->distinct()->asArray()->all();
$all_ips = [];
foreach ($ips as $ip) {
    $new_row = [];
    $new_row['ip'] = $ip['ip'];
    $new_row['another'] = UserIpLog::find()->where(['ip' => $ip['ip']])->andWhere('user_id <> ' . $user->id)->groupBy('user_id')->orderBy('date_add DESC')->with('user')->all();
    $all_ips[] = $new_row;
}

?>
<h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
<div class="row">
    <div class="col-lg-2">
        <?= $this->render('menu', ['user_id' => $user->id]) ?>
    </div>
    <div class="col-lg-10">
        <table class="table">
            <tr>
                <th>Дата</th>
                <th>ip</th>
                <th>Браузер (версия)</th>
                <th>Операционная система (версия)</th>
            </tr>
            <tbody>
            <?php foreach ($logs as $log) {
                ?>
                <tr>
                    <td><?= $log->date_add ?></td>
                    <td><?= $log->ip ?></td>
                    <td><?= $log->browser . ' (' . $log->browser_version . ')' ?></td>
                    <td><?= $log->os . ' (' . $log->os_version . ')' ?></td>
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
        <br>
        <h4>Совпадения с ip пользователя</h4>
        <table class="table">
            <tr>
                <th>ip Совпадения</th>
                <th>Последний вход</th>
                <th>Пользователь</th>
                <th>Браузер (версия)</th>
                <th>Операционная система (версия)</th>
            </tr>
            <?php
            foreach ($all_ips as $ip) {
                $ip_show = false;
                foreach ($ip['another'] as $another) {
                ?>
                <tr>
                    <td><?= $ip_show ? null : $ip['ip'] ?></td>
                    <td><?= $another->date_add ?></td>
                    <td><a href="/user/<?= $another->user->id ?>"><?= $another->user->username.'( id:'.$another->user->id.')' ?></a></td>
                    <td><?= $another->browser . ' (' . $another->browser_version . ')' ?></td>
                    <td><?= $another->os . ' (' . $another->os_version . ')' ?></td>
                </tr>
            <?php $ip_show = true;}
            } ?>
        </table>
    </div>
</div>

