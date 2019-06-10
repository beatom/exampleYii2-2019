<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = 'Подтверждение документов';
$this->params['breadcrumbs'][] = [  'label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<table class="table">

    <tr>
        <th>id</th>
        <th>Логин</th>
        <th>ФИО</th>
        <th>Дата добавления</th>
        <th></th>
    </tr>

    <?php
    foreach ($models as $model) { ?>
        <tr>
        <td><?= $model->user->id ?></td>
        <td> <?= $model->user->username ?></td>
        <td> <?= $model->user->firstname . ' '.$model->user->lastname  ?></td>
        <td><?= $model->date_add ?></td>
        <td> <a href="<?= Url::to(['/user/verification/'. $model->user->id ]) ?>">Проверить</a></td>
        </tr><?php
    }?>

</table>

<?php
// отображаем постраничную разбивку
echo LinkPager::widget([
    'pagination' => $pages,
]);
?>
