<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Промо баннеры';
$this->params['breadcrumbs'][] = $this->title;
?>


<?php  Pjax::begin(); ?>
<table class="table">
    <div class="form-group">
        <a class="btn btn-primary" href="<?= Url::to('/promo/add') ?>">Добавить баннер</a>
    </div>
    <tr>
        <th>id</th>
        <th>Название</th>
        <th>Дата добавления</th>
        <th>Отображать пользователям</th>
        <th></th>
    </tr><?php

    foreach ($models as $model) { ?>

        <tr>
            <td><?= $model->id ?></td>
            <td><?= $model->name ?></td>
            <td><?= $model->date_add ?></td>
            <td> <?= $model->show ? 'Да' : '-' ?></td>
            <td><a href="/promo/edit/<?= $model->id ?>">Подробнее</a></td>
        </tr>

        <?php
    } ?>

</table>

<?php
// отображаем постраничную разбивку
echo \common\service\Servis::getInstance()->getPaginator($pages);
Pjax::end();
?>
