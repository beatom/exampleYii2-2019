<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Редактирование бонуса';
$this->params['breadcrumbs'][] = [  'label' => 'Бонусы', 'url' => ['/site/bonus-log']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-add-bonus']);?>
            <?= $form->field($model, 'summ_now')->input('text')->label('Не использованная сумма. Сумма которую можно инвестировать') ?>
            <?php
            if($model->id){?>
                <a href="/site/bonus-invest/<?= $model->id ?>" class="btn btn-success">Инвестировать бонусные средства</a>
                <?= $form->field($model, 'id')->input('hidden')->label('')?>
                <?= $form->field($model, 'summ')->input('text')->label('Сумма') ?>
	            <?= $form->field($model, 'user_id')->input('input',[
	                    'list' => "users__list",
                        'placeholder' => 'Введите id, username, имя или фамилию пользователя',
                        'class' => 'first-deposit-user_id find-user__list  form-control'
                ])->label('Пользователь') ?>

                <datalist id="users__list"></datalist>


                <?= $form->field($model, 'date_add')->input('date',['readonly'=>''])->label('Дата начала') ?>
                <?php
            }
            else{?>

	            <?= $form->field($model, 'user_id')->input('input',[
	                    'list' => "users__list",
                        'placeholder' => 'Введите id, username, имя или фамилию пользователя',
                        'class' => 'first-deposit-user_id find-user__list form-control'
                ])->label('Пользователь') ?>

                <datalist id="users__list"></datalist><?php
            } ?>

            <?= $form->field($model, 'date_end')->input('date')->label('Дата окончания') ?>

            <?= $form->field($model, 'work_days')->input('text')->label('Количество дней. Если не установить дату окончания, будет доступен количество дней с момента активации') ?>
            <p></p>

            <?= $form->field($model, 'description')->input('text')->label('Комментарий') ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
