<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;use yii\bootstrap\ActiveForm;

$this->title = 'Цель до ' . $model->max_sum . '$';
$this->params['breadcrumbs'][] = [  'label' => 'Настройки', 'url' => ['/seting/index']];
$this->params['breadcrumbs'][] = [  'label' => 'Цели', 'url' => ['/seting/objectives']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h2><?=Html::encode($this->title)?></h2>
<br>
<a class="btn btn-primary" href="<?= Url::to('/seting/add_objective_stage/' .$model->id ) ?>" style="margin-bottom: 20px">Добавить уровень</a>

<table class="table">

    <tr>
        <th>id</th>
        <th>Процент</th>
        <th>Заголовок</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($models as $m) { ?>
        <tr>
            <td><?= $m->id ?></td>
            <td><?= $m->stage ?></td>
            <td><?= $m->title ?></td>
            <td>
                <a href="/seting/edit_objective_stage/<?= $m->id ?>">Редактировать уровнь</a>
            </td>
            <td>
                <a style="color: red" href="/seting/delete_objective_stage/<?= $m->id ?>" data-confirm="Вы действительно хотите удалить этот уровень?">Удалить</a>
            </td>
        </tr>
    <?php  } ?>
</table>

