<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;

$this->title = 'Акции';


$get = $_GET;
$url = (!empty($get))? '?'.http_build_query($get) : '?';
if( isset($get['order_by']) && $get['order_by'] == 1 ){
	$span = '<span class="glyphicon glyphicon-sort-by-attributes"></span>';
}
else{
	$span = '<span class="glyphicon glyphicon-sort-by-attributes-alt"></span>';
}

?>

<div class="site-index">

        <h1>Акции</h1>

    <div class="body-content">
        <div class="form-group">
            <a class="btn btn-primary" href="<?= Url::to('/shares/add') ?>">Добавить</a>
        </div>

        <table class="table">

            <tr>
                <th>id</th>
                <th>title</th>
                <th>Анотация</th>
                <th>
	                <?php
	                $flag = false;
	                if(isset($get['fild']) && $get['fild'] == 'date_start'){
		                $href = Url::to(['/shares/index'.$url.'&order_by='.(empty($_GET['order_by'])?'1':0)]);
		                $flag = true;
	                }
	                else{
		                $href = Url::to(['/shares/index'.$url.'&fild=date_start']);
	                }
	                ?>
                    <a href="<?= $href ?>">Дата начала<?php echo ( $flag )? $span : '' ?></a>
                </th>
                <th>
	                <?php
	                $flag = false;
	                if(isset($get['fild']) && $get['fild'] == 'date_end'){
		                $href = Url::to(['/shares/index'.$url.'&order_by='.(empty($_GET['order_by'])?'1':0)]);
		                $flag = true;
	                }
	                else{
		                $href = Url::to(['/shares/index'.$url.'&fild=date_end']);
	                }
	                ?>
                    <a href="<?= $href ?>">Дата окончания<?php echo ( $flag )? $span : '' ?></a>
                </th>
                <th>
	                <?php
	                $flag = false;
	                if(isset($get['fild']) && $get['fild'] == 'status'){
		                $href = Url::to(['/shares/index'.$url.'&order_by='.(empty($_GET['order_by'])?'1':0)]);
		                $flag = true;
	                }
	                else{
		                $href = Url::to(['/shares/index'.$url.'&fild=status']);
	                }
	                ?>
                    <a href="<?= $href ?>">статус<?php echo ( $flag )? $span : '' ?></a>
                </th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                <td><?= $model->id ?></td>
                <td> <?= $model->title ?></td>
                <td> <?= $model->text_small ?></td>
                <td><?= date('d-m-Y', strtotime($model->date_start)) ?></td>
                <td><?= date('d-m-Y', strtotime ($model->date_end)) ?></td>
                <td><?= ($model->status)? 'включена' : 'выключена' ?></td>
                <td>
                    <a href="<?= Url::to(['shares/edit', 'id' => $model->id ]) ?>"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="<?= ( '//'. \Yii::$app->params['frontendDomen']. '/shares/'.$model->id.'-'.$model->synonym )?>"><span class="glyphicon glyphicon-eye-open"></span></a>
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
