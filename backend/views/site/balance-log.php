<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\BalanceLog;
use yii\helpers\Url;

$this->title = 'Движение средств';

$status = (isset($_GET['status']))?$_GET['status']:0;
$operation = (isset($_GET['operation']))?$_GET['operation']:0
?>

<div class="site-index">

    <h1><?= $this->title ?></h1>

    <div class="body-content">
        <div class="form-group">
            <p>Итог по результату</p>
            введено: <?= $query_info_in ?>$
            <br>
            выведено: <?= $query_info_out ?>$
        </div>

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>

            <?= Html::textInput('user', ( (isset($_GET['user']))? $_GET['user']: '' ),['placeholder'=>'пользователь']) ?>

            <?= Html::dropDownList('status',[], ['-1' => ''] + BalanceLog::$status_name, ['options' => [$status => ['selected' => 'selected' ]]]) ?>
            <?= Html::dropDownList('operation',[], ['-1' => ''] + BalanceLog::getOperationNames(), ['options' => [$operation => ['selected' => 'selected' ]]]) ?>

            с <input type="date" name="date_to" value="<?= (isset($_GET['date_to']))? $_GET['date_to']: '' ?>">
            до <input type="date" name="date_from" value="<?= (isset($_GET['date_from']))? $_GET['date_from']: '' ?>">


            <?= Html::submitButton('Выбрать', ['class'=>'btn btn-primary']) ?>
           <?php
           if(isset($_GET['operation'])) {
               if($_GET['operation'] == '1' OR $_GET['operation'] === '0') { ?>
                   <button type="submit" class="btn btn-warning" name="export">Экспорт</button>
            <?php   }
           }
           ?>
            <?= Html::endForm()?>

        </div>

        <table class="table">

            <tr>
                <th>id</th>
                <th>Дата</th>
                <th>Пользователь</th>
                <th>Сумма</th>
                <th>система</th>
                <th>Операция</th>
                <th>Статус</th>
                <th>Комментарий</th>
                <th>Sms</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) {
                $log_date = $model->date_add;
                if($model->date_add != $model->execution_time) {
                    $log_date = $model->execution_time ? $model->date_add . '</br><b>' . $model->execution_time . '</b>' : $model->date_add . '</br>(-)';
                }
                ?>
                <tr>
                <td> <?= $model->id ?> </td>
                <td> <?= $log_date ?> </td>
                <td> <?php if(!$model->user) { echo '-';} else { echo Yii::$app->user->can('admin') ? '<a href="'.Url::to('/user/'.$model->user->id).'">'.$model->user->username.' ( '.$model->user->id.' )</a>' : $model->user->username;} ?> <?= $model->user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></td>
                <td> <?= $model->summ ?></td>
                <td> <?= BalanceLog::$system[$model->system] ?> <?= $model->payway_id ?  '('.BalanceLog::$payways[$model->payway_id].')' : null ?></td>
                <td> <?= BalanceLog::$operation_name[$model->operation] ?></td>
                <td> <?= BalanceLog::$status_name[$model->status] ?></td>
                <td> <?= $model->comment ?></td>
                <td><?php
                    if( $model->sms) {?>
                        <input type="checkbox" checked onclick="return false;"/>
                        <?php
                    }
                    ?>
                </td>
                <td><?php
                    if( $model->operation == BalanceLog::transfer && $model->status == BalanceLog::in_processing) {?>
                    <a href="<?= Url::to('/site/change_transfer?id=' . $model->id) ?>"><span class="glyphicon glyphicon-pencil"></span></a><?php
                    }
                    else if($model->operation == BalanceLog::exit_deposit && in_array($model->status, [BalanceLog::in_processing, 3])){
                        echo '<a href="'.Url::to('/site/change_cashout?id=' . $model->id).'"><span class="glyphicon glyphicon-pencil"></span></a>';
                    }
                    ?>
                </td>

                </tr><?php
            }?>

        </table>

        <?php
        // отображаем постраничную разбивку
        echo LinkPager::widget([
            'pagination' => $pages,
        ]);

        ?>

    </div>
</div>