<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Заблокированные номера телефонов';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel-group">
    <?= Html::beginForm('#', 'get'); ?>

    <div class="form-group">
        <?= Html::input('text', 'phone', ( isset($_GET['phone'])? $_GET['phone'] : '' ), ['placeholder'=>'поиск по номеру'] )?>

        <?php
        $block_type = ['' => 'Все блокировки', 0 => 'Временная', 1 => 'Постоянная'];
        ?>
        <?= Html::dropDownList('type',  isset($_GET['type'])? $_GET['type'] : '', $block_type) ?>
    </div>

    <?= Html::submitButton( 'Выбрать', ['class'=>'btn btn-primary'] )?>
    <?= Html::endForm(); ?>
</div>

<?php

$get = $_GET;
$url = (!empty($get))? '?'.http_build_query($get) : '?';

if( isset($get['order_by']) && $get['order_by'] == 1 ){
    $span = '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
}
else{
    $span = '<span class="glyphicon glyphicon-sort-by-attributes"></span>';
}
?>

<?php  Pjax::begin(); ?>
<table class="table">

    <tr>
        <th>id</th>
        <th>Телефон</th>
        <th>Блокировка</th>
        <th>Время окончания блокировки</th>
        <th>Статус блокировки</th>
        <th>Комментарий</th>
        <th></th>
    </tr>
    <?php foreach ($models as $model) { ?>
        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->phone ?></td>
            <td><?= $model->type ? 'Постоянная' : 'Временная' ?></td>
            <td><?= $model->date_end ? date('d-m-Y H:i', strtotime($model->date_end)) : '-' ?></td>
            <td><?= $model->active ? 'Активна' : '-' ?></td>
            <td><?= $model->comment ?></td>
            <td>
                <?= ($model->type AND $model->active) ? '<a href="/sms/blocklist'.$url.'&unblock='.$model->id.'">Разблокировать</a>' : null ?>
            </td>

        </tr>
        <?php  } ?>
</table>

<?php
// отображаем постраничную разбивку
echo \common\service\Servis::getInstance()->getPaginator($pages);
Pjax::end();
?>
