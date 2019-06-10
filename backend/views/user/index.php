<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\Pjax;

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="panel-group">
	<?= Html::beginForm('#', 'get'); ?>

    <div class="form-group">
        <?= Html::input('text', 'user_id', ( isset($_GET['user_id'])? $_GET['user_id'] : '' ), ['placeholder'=>'id пользователя'] )?>
		<?= Html::input('text', 'username', ( isset($_GET['username'])? $_GET['username'] : '' ), ['placeholder'=>'имя пользователя'] )?>
		<?= Html::input('text', 'email', ( isset($_GET['email'])? $_GET['email'] : '' ), ['placeholder'=>'email'] )?>
        <?= Html::input('text', 'city', ( isset($_GET['city'])? $_GET['city'] : '' ), ['placeholder'=>'город'] )?>

		<?php
		$status_id = (isset($_GET['status_in_partner']))? $_GET['status_in_partner'] : -1
		?>
		<?= Html::dropDownList('status_in_partner', $status_id, ['-1' => 'статус в партнерке'] + User::$partner_staus,[
			'style'=> 'height: 26px;'
		]) ?>
    </div>

    <div class="form-group">
        Дата регистрации с
		<?= Html::input('date', 'date_from', ( isset($_GET['date_from'])? $_GET['date_from'] : '' )) ?>
        по
		<?= Html::input('date', 'date_to', ( isset($_GET['date_to'])? $_GET['date_to'] : '' )) ?>
    </div>

	<?= Html::submitButton( 'Выбрать', ['name'=>'filtr', 'class'=>'btn btn-primary'] )?>
	<?= Html::submitButton( 'экспорт', ['name'=>'export', 'class'=>'btn btn-warning'] )?>
	<?= Html::endForm(); ?>
</div>

<?php
//echo '<pre>'; var_dump( http_build_query( $_GET)); echo'</pre>';
//die;
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
        <th><?php
	        $flag = false;
	        if(isset($get['fild']) && $get['fild'] == 'id'){
		        $href = Url::to(['/user/index'.$url.'&order_by='.(empty($_GET['order_by'])?'1':0)]);
		        $flag = true;
	        }
	        else{
		        $href = Url::to(['/user/index'.$url.'&fild=id']);
	        }
	        ?>
            <a href="<?= $href ?>">id<?php echo ( $flag )? $span : '' ?></a>
        </th>
        <th>Логин</th>
        <th>ФИО</th>
        <th>email</th>
        <th>Телефон</th>
        <th>Страна/Город</th>
        <th>Дата регистрации</th>
        <th>Дата рожденья</th>
        <th>Роль</th>
        <th><?php
            $flag = false;
            if(isset($get['fild']) && $get['fild'] == 'balance'){
                $href = Url::to(['/user/index'.$url.'&order_by='.(empty($_GET['order_by'])?'1':0)]);
                $flag = true;
            }
            else{
	            $href = Url::to(['/user/index'.$url.'&fild=balance']);
            }
            ?>
            <a href="<?= $href ?>">Баланс<?php echo ( $flag )? $span : '' ?></a>
        </th>
        <th>Бонус</th>
        <th>Партнерский счет</th>
        <th>Балы</th>
        <th>Статус</th>
        <th><?php
	        $flag = false;
	        if(isset($get['fild']) && $get['fild'] == 'personal_contribution'){
		        $href = Url::to(['/user/index'.$url.'&order_by='.(empty($_GET['order_by'])?'1':0)]);
		        $flag = true;
	        }
	        else{
		        $href = Url::to(['/user/index'.$url.'&fild=personal_contribution']);
	        }
	        ?>
            <a href="<?= $href ?>">Введено<?php echo ( $flag )? $span : '' ?></a>
        </th>

        <th>Вход на сайт</th>
        <th></th>
    </tr><?php

	foreach ($users as $user) { ?>

        <tr>
            <td><?= $user['id'] ?></td>
            <td> <a href="/user/<?= $user['id']  ?>"><?= $user['username'] ?></a></td>
            <td> <?= $user['fio']  ?></td>
            <td> <?= $user['email']  ?></td>
            <td> <?= $user['phone']  ?></td>
            <td><?= $user['adress']  ?></td>
            <td> <?= $user['date_reg']  ?></td>
            <td> <?= $user['date_bithday']  ?></td>
            <td><?= $user['role']  ?></td>
            <td><?= $user['balance'] ?></td>
            <td><?= $user['balance_bonus']  ?></td>
            <td><?= $user['balance_partner']  ?></td>
            <td><?= $user['ball_invest']  ?></td>
            <td><?= $user['stp']  ?></td>
            <td><?= User::$partner_staus[$user['stp']] ?></td>
            <td><?= $user['last_login'] ?></td>
            <td><a href="/user/<?= $user['id']  ?>">Подробнее</a></td>
        </tr>

		<?php
	} ?>

</table>

<?php
// отображаем постраничную разбивку
echo \common\service\Servis::getInstance()->getPaginator($pages);
Pjax::end();
?>
