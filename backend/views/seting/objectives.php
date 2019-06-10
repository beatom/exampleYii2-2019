<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;use yii\bootstrap\ActiveForm;

$this->title = 'Цели';
$this->params['breadcrumbs'][] = [  'label' => 'Настройки', 'url' => ['/seting/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?=Html::encode($this->title)?></h1>
<?php  Pjax::begin(); ?>
<table class="table">

    <tr>
        <th>id</th>
        <th>Максимальная сумма (до)</th>
        <th>К-во уровней</th>
        <th></th>
        <th></th>
    </tr>
    <?php foreach ($models as $m) { ?>
        <tr>
            <td><?= $m->id ?></td>
            <td><?= $m->max_sum ?></td>
            <td><?= \common\models\ObjectiveStage::find()->where(['objective_id' => $m->id])->count() ?></td>
            <td>
                <a href="/seting/edit_objective/<?= $m->id ?>">Редактировать уровни</a>
            </td>
            <td>
                <a style="color: red" href="/seting/delete_objective/<?= $m->id ?>" data-confirm="Вы действительно хотите удалить данную цель и ее уровни?">Удалить</a>
            </td>
        </tr>
    <?php  } ?>
</table>

<?php
// отображаем постраничную разбивку
echo \common\service\Servis::getInstance()->getPaginator($pages);
Pjax::end();
?>
<br><br>
<hr>
<h3>Добавить цель</h3>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['id' => 'form-add-objective']); ?>

        <?= $form->field($model, 'max_sum')->input('number')->label('Максимальная сумма') ?>
        <div class="form-group">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary',]) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
