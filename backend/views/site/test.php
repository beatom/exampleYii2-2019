<?php


use yii\helpers\Html;
use yii\helpers\Url;
use common\models\User;

$this->title = 'Тест';
$this->params['breadcrumbs'][] = $this->title;


$arr_task = [
    'actionChangeStatus'=>'изменение статуса партнера',
    'actionBonusesForMonth'=>'Бонусная программа "Бонусы по итогам месяца" и "Депозит"',
    'actionAttractionInvestors'=>'начисление балов за привлеченных инвесторов',
    'actionAttractionPartner'=>'начисление балов за привлеченных партнеров',
];

?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p class="alert">
        1. Изменять только тестовых пользователей. Иначе может произойти не поправимое
        <br>2. Статус и Партнеров можно поменять <a href="<?= Url::to('/user/index') ?>">тут.</a> Выберите пользователя и меню партнерка.
        <br>3. После установки данных для проверки изменения статуса можно запустить как войдя в кабинет пользователя так и крон задачей.
        <br>4. Депозит - когда пользователь вкладывает реальные деньги через( криптонатор или другую систему), начисляем по таблице за операцию.
        <br>5. Видео-отзыв о invest, Отзыв о сотрудничестве с invest, - выставляются в админке конкретному пользователю, после обращения пользователем.
        <br>6. Основной доход - начисление происходит в момент выплаты.
    </p>

    <?php
    if($message){
        echo '<p class="alert" style="background: #c1aa89">'.$message.'</p>';
    }
    ?>

    <div class="form-group">
        <div class="col-md-5">
        <h3>Задачи</h3>
        <?= Html::beginForm('#','task', ['name'=>'task-form']) ?>

        <?= Html::radioList('task', null,$arr_task,
        ['item' => function ($index, $label, $name, $checked, $value) {
            return
                '<div class="radio"><label>' . Html::radio($name, $checked, ['value' => $value]) . $label . '</label></div>';
             },
        ]
        ) ?>

        <?= Html::submitButton('работать', ['class'=> 'btn btn-primary']) ?>
        <?= Html::endForm()?>
        </div>

        <div class="col-md-5">
            <h3>Запланировано</h3>
            <?php
            foreach ($queues as $queue){
                echo '<br>'. $arr_task[$queue->task];
            }
            ?>
        </div>


    </div>
    <div style="clear: both"></div>

    <div class="row">
        <h3>Установить данные</h3>
        <?= Html::beginForm('#', 'post', ['name'=>'set-test-data']) ?>
        <table class="table">
            <tr>
               <th>id user</th>
               <th>username</th>
               <th>id партнера</th>
               <th>сумма "привлеченный капитал" за текущий месяц</th>
               <th>сумма "привлеченный капитал" за все время</th>
               <th>внесенные личные средства</th>
               <th>установить "основной" доход</th>
               <th>Статус</th>
            </tr>
            <?php
            foreach ($partners_info as $partner_info){
                $tmp = User::findIdentity($partner_info->user_id);

                $sql = 'SELECT SUM(summ) as summ from `balance_partner_log` WHERE user_id = '.$partner_info->user_id .' AND status = '.\common\models\BalancePartnerLog::status_come_in;
                $summ = Yii::$app->db->createCommand($sql)->queryScalar();
                $summ = ($summ)? $summ : 0;

                echo '<tr>';
                echo '<td><a href="'.Url::to('/user/'.$partner_info->user_id).'"> '.$partner_info->user_id.'</a></td>';
                echo '<td><a href="'.Url::to('/user/'.$partner_info->user_id).'"> '.$tmp->username.'</a></td>';
                echo '<td><a href="'.Url::to('/user/'.$tmp->partner_id).'">'.(($tmp->partner_id)?$tmp->partner_id:0).'</a></td>';
                echo '<td>'.Html::input('text', 'data['.$partner_info->user_id.'][sum_in_mount]',  $partner_info->sum_in_mount).'</td>';
                echo '<td>'.Html::input('text', 'data['.$partner_info->user_id.'][sum_in_all]',  $partner_info->sum_in_all).'</td>';
                echo '<td>'.Html::input('text', 'data['.$partner_info->user_id.'][personal_contribution]',  $partner_info->personal_contribution).'</td>';
                echo '<td>'.Html::input('text', 'data['.$partner_info->user_id.'][Basic_Income]',  $summ).'</td>';
                echo '<td>'.Html::dropDownList('data['.$partner_info->user_id.'][status_in_partner]', $tmp->status_in_partner, User::$partner_staus).'</td>';
                echo '</tr>';
            }

            ?>
        </table>
        <?= Html::submitButton('сохранить', ['class'=> 'btn btn-primary']) ?>
    </div>

</div>
