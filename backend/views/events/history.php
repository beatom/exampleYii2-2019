<?php

use yii\helpers\Url;
use common\service\Servis;
use yii\widgets\Pjax;
use common\models\Events;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}
$this->title = $seo['title'];

$service = Servis::getInstance();
$this->params['breadcrumbs'][] = ['label' => 'Статистика по дням', 'url' => ['/events/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $this->title ?></h3>
<div class="row">
    <div class="col-lg-12">
        <?= $this->render('_menu') ?>

        <div class="panel-group">
            <?= Html::beginForm('', 'get') ?>
            День <input type="date" name="date_add" value="<?= (isset($_GET['date_add']))? $_GET['date_add']: '' ?>">
            <?= Html::submitButton('Выбрать', ['class'=>'btn btn-primary']) ?>
            <?= Html::endForm()?>
        </div>

        <?php
        Pjax::begin();
        ?>
        <table class="table">
            <tr>
                <th>id</th>
                <th>Событие</th>
                <th>Дата</th>
                <th>% от банка</th>
                <th>Ставка</th>
                <th>Коэффициент</th>
                <th>Букмекер</th>
                <th>Результат</th>
                <th>Бесплатно</th>
                <th>Отображается</th>
                <th></th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) {
                $additional_time = null;
                if (date('H', strtotime($model->date_add)) >= 15) {
                    $additional_time = ' +1 day';
                }
                $event_counted = date('Y-m-d H:i:s') > date('Y-m-d 10:00:00', strtotime($model->date_add . $additional_time)) ? true : false;
                ?>
                <tr>
                    <td><?= $model->id ?></td>
                    <td><?= $model->title ?></td>
                    <td><?= $model->date_add ?></td>
                    <td><?= $model->bank_percent ?>%</td>
                    <td><?= $model->bet ?></td>
                    <td><?= $model->coefficient ?></td>
                    <td><?= $model->bookmaker ?></td>
                    <td style="<?= $model->result ? 'color:' . Events::$results_colors[$model->result] : null ?>"><?= Events::$results[$model->result] ?></td>
                    <td><?= $model->free ? "+" : "-" ?></td>
                    <td><?= $model->show ? "+" : "-" ?></td>
                    <?php if (!$event_counted) { ?>
                        <td>
                            <a href="/events/edit/<?= $model->id ?>">Редактировать</a>
                        </td>
                        <td>
                            <a href="/events/delete/<?= $model->id ?>" data-confirm="Вы действительно хотите удалить данное событие?"><span class="glyphicon glyphicon-trash" style="color: red"></span></a>
                        </td>
                    <?php } else { ?>
                        <td></td>
                        <td></td>
                    <?php } ?>
                </tr>
                <?php
            } ?>

        </table>
        <?php
        // отображаем постраничную разбивку
        echo $service->getPaginator($pages);

        Pjax::end();
        ?>
    </div>
</div>
