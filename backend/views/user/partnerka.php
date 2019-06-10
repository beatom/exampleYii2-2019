<?php

use yii\helpers\Html;
use common\models\User;
use yii\helpers\Url;
use common\models\UserPartnerInfo;
use common\service\Servis;
use common\models\trade\InvestmentLog;
use common\models\BalancePartnerLog;

$this->title = $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = $this->title;
$service = Servis::getInstance();

?>
<div class="site-signup">
    <h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
    <div class="row">
        <div class="col-lg-2">
            <?= $this->render('menu', ['user_id' => $user->id]) ?>
        </div>
        <div class="col-lg-10">
            <h2>Информация о пользователе</h2>
            <h4>Старший партнер: <?= $user->partner_id ? '<a href="/user/' . $user->partner_id . '">' . User::findIdentity($user->partner_id)->username . ' id:' . $user->partner_id . '</a>' : 'Нет' ?></h4>
            <br><br>

            <h4>информация по партнерке</h4>
            <table class="table table-bordered">
                <tr>
                    <td>Привлеченный капитал ( изменение статуса )</td>
                    <td><?=  $service->beautyDecimal($user->getInvitedFounds()) ?></td>
                </tr>
                <tr>
                    <td>Личный вклад ( изменение статуса )</td>
                    <td><?= $service->beautyDecimal($user->getBalance()) ?></td>
                </tr>
            </table>
            <br>

            <?= Html::beginForm('#', 'post', ['name' => 'change-status']) ?>
            <div class="form-group">
                <label><?= Html::dropDownList('status_in_partner', $user->status_in_partner, User::$partner_staus) ?> Статус пользователя</label>
            </div>

            <h4>Партнеры</h4>

            <div class="form-group">
                <label><input type="text" id="addpartner" name="addpartner" value=""> Добавить партнера</label>
                <p>для добавления партнера впишите id поьзователя</p>
            </div>

            <table class="table">
                <tr>
                    <th>id</th>
                    <th>Ник</th>
                    <th>Статус</th>
                    <th>Линия</th>
                    <th>Дата пополнения</th>
                    <th>На счету</th>
                    <th>Полный баланс</th>
                    <th>Гонорар invest</th>
                    <th>Мой кэшбэк</th>
                </tr>

                <?php
                $lines = unserialize($partner_info->arr_line);
                foreach ($table as $model) {
                    foreach ($lines as $key => $arr) {
                      if(in_array(strval($model->id), $arr)) {
                          $line = $key;
                          break;
                      }
                    }
                    $line_style = '';
                    if($line > $user->status_in_partner + 1 ){
                        $line_style = 'background: gainsboro;';
                    }
                    ?>
                    <tr style="<?= $line_style ?>">
                        <td><?= $model->id ?></td>
                        <td><a target="_blank" href="/user/<?= $model->id ?>"><?= $model->username ?></a></td>
                        <td><?= User::$partner_staus[$model->status_in_partner] ?></td>
                        <td><?= $line ?></td>
                        <td><?= $model->first_deposit_date ?></td>
                        <td><?= $service->beautyDecimal($model->balance) ?></td>
                        <td><?= $service->beautyDecimal($model->total_b) ?></td>
                        <td><?= $service->beautyDecimal($model->difference) ?></td>
                        <td><?= $service->beautyDecimal($model->result) ?></td>
                    </tr>
                <?php }
                ?>
            </table>

            <input type="hidden" id="delpartner" name="delpartner" value="">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>

            <?= Html::endForm() ?>

        </div>
    </div>
</div>
<script>

    <?php
    if ($message) {
        echo 'alert("' . $message . '")';
    }
    ?>

    function delpartner(id) {
        var t = $('#delpartner').val();
        $('#delpartner').val(t + '|' + id.toString());
        $('#' + id.toString()).hide();
    }
</script>
