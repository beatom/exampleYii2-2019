<?php

use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\service\Servis;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

if(\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}
$this->title = $seo['title'];

$service = Servis::getInstance();

$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $this->title ?></h3>
<div class="row">
    <div class="col-lg-12">
        <?= $this->render('_menu') ?>
        

        <table class="table">
            <tr>
                <th>Старт</th>
                <th>Начисления</th>
                <th>Начальная сумма</th>
                <th>Конечная сумма</th>
                <th>Прибыль %</th>
                <th>Прибыль $</th>
                <th></th>
                <th></th>
            </tr>

            <?php foreach ($models as $day) { ?>
                <tr>
                    <td> <?= date('Y-m-d 15:00', strtotime($day->date_add)) ?></td>
                    <td><?= date('Y-m-d 10:00', strtotime($day->date_add . ' +1 day')) ?></td>
                    <td><?= $service->beautyDecimal($day->sum_start, 2) ?>$</td>
                    <td><?= $service->beautyDecimal($day->sum_end, 2) ?>$</td>
                    <td><?= $service->beautyDecimal($day->profit, 2) ?>% </td>
                    <td><?= $service->beautyDecimal($day->sum_start + $day->sum_start * ($day->profit / 100), 2) ?>$</td>
                    <td><a href="/events/report/<?= $day->id ?>"><?= $day->comment ? 'Отчет' : 'Расчеты' ?></a></td>
                </tr>

                <?php
            } ?>

        </table>

        <?php
        // отображаем постраничную разбивку
        echo $service->getPaginator($pages);
        ?>
        
    </div>
</div>
