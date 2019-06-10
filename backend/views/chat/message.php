<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\trade\TradingAccount;
use kartik\widgets\DateTimePicker;
use common\models\ChatMessage;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Чат', 'url' => ['/chat/index']];
$this->params['breadcrumbs'][] = $this->title;


function showChildReviews($id)
{
    $child_messages = ChatMessage::find()->where(['deleted_at' => null, 'parent_id' => $id])->with(['user'])->all();
    if (!$child_messages) {
        return;
    }
    echo '<ul class="tree__list">';
    $i = 1;
    $c = count($child_messages);
    foreach ($child_messages as $r) { ?>
        <li class="<?= $i++ == $c ? 'lastlist': null?>">
            <div style="display: ruby-text;">
                id:<?= $r->id ?> | Пользователь: <a href="/user/<?= $r->user_id ?>" target="_blank"><?= $r->user->getUserForList()['string'] ?></a> |
                <?= date('d-m-Y H:i:s', strtotime($r->date_add)) ?> | <span class="glyphicon glyphicon-thumbs-up" style="color: green" aria-hidden="true"></span>
                <?= $r->likes ?> | <span class="glyphicon glyphicon-thumbs-down" style="color: red" aria-hidden="true"></span> <?= $r->dislikes ?>
                <div class="review__buttons">
                    <button type="button" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#comment<?= $r->id ?>">Показать отзыв</button>
                    <a type="button" class="btn btn-info btn-sm" href="/chat/edit/<?= $r->id ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <a type="button" class="btn btn-danger btn-sm" href="/chat/delete/<?= $r->id ?>" data-confirm="Вы действительно хотите удалить отзыв id:<?= $r->id ?>?"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                </div>
            </div>
            <div id="comment<?= $r->id ?>" class="collapse review__comment">
                <?= $r->text ?>
            </div>
        </li>
        <?php
        showChildReviews($r->id);
    }
    echo '</ul>';

}

?>

<h3><?= Html::encode($this->title) ?></h3>
<style>
    ul.tree__list {
        padding: 0;
        margin: 0;
        list-style-type: none;
        position: relative;
        margin-left: 25px;
    }

    ul.tree__list li {
        list-style-type: none;
        border-left: 2px solid #000;
        /*margin-left: 1em;*/
        padding-bottom: 5px;
    }

    ul.tree__list li div {
        padding-left: 1em;
        position: relative;
    }

    ul.tree__list li > div:first-child::before {
        content: '';
        position: absolute;
        top: 0;
        left: -2px;
        bottom: 50%;
        width: 0.75em;
        border: 2px solid #000;
        border-top: 0 none transparent;
        border-right: 0 none transparent;
    }

    ul.tree__list > li:last-child, ul.tree__list > ul:last-child, ul.tree__list li.lastlist {
        border-left: 2px solid transparent;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'user_id', ['errorOptions' => ['class' => 'help-block', 'encode' => false]])->input('input', ['list' => "users__list", 'placeholder' => 'Введите id, username, имя или фамилию пользователя', 'class' => 'form-control first-deposit-user_id find-user__list', 'disabled' => ($model->user_id AND $model->id) ? true : false, 'value' => $model->user_id ? $model->user_id : '']) ?>
        <?= ($model->user_id AND isset($model->id)) ? 'Пользователь: <a href="/user/' . $model->user_id . '">' . $model->user->getUserForList()['string'] . '</a><hr/>' : null ?>
        <datalist id="users__list">
            <?php if ($model->user_id AND isset($model->id)) { ?>
                <option class="trader" value="<?= $model->user_id ?>"><?= $model->user->getUserForList()['string'] ?></option>
            <?php } ?>
        </datalist>
        <?= $form->field($model, 'text')->textarea() ?>
        <?= $form->field($model, 'likes')->textInput(['type' => 'number']) ?>
        <?= $form->field($model, 'dislikes')->textInput(['type' => 'number']) ?>
        <?php
        $plugin_options = [
            'format' => 'yyyy-MM-dd hh:i:s',
            'autoclose' => true,
            'weekStart' => 1, //неделя начинается с понедельника
            'todayBtn' => true, //снизу кнопка "сегодня"
            'endDate' => date('Y-m-d H:i:s')
        ];

        echo $form->field($model, 'date_add')->widget(DateTimePicker::class, [
            'name' => 'dp_1',
            'type' => DateTimePicker::TYPE_INPUT,
            'options' => ['placeholder' => 'Ввод даты/времени...'],
            'convertFormat' => true,
            'value' => date("Y-m-d h:i:s", (integer)$model->date_add),
            'pluginOptions' => $plugin_options
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Сохранить') ?>
            <?php if ($model->id) { ?>
                <a type="button" class="btn btn-danger" href="/chat/delete/<?= $model->id ?>" data-confirm="Вы действительно хотите удалить этот отзыв?">Удалить</a>
            <?php } ?>
        </div>
        <br>
        <?php ActiveForm::end(); ?>

        <?php
        if($model->id) {
            echo '<a class="btn btn-success" href="/chat/add/'. $model->id .'">Добавить ответное сообщение</a>';
        }

        if (!empty($review_tree)) {
            $parent = $model; ?>
            <h4>Ветка отзыва</h4>
            <hr>
            <div style="display: ruby-text;">
                id:<?= $parent->id ?> | Пользователь: <a href="/user/<?= $parent->user_id ?>" target="_blank"><?= $parent->user->getUserForList()['string'] ?></a> |
                <?= date('d-m-Y H:i:s', strtotime($parent->date_add)) ?> | <span class="glyphicon glyphicon-thumbs-up" style="color: green" aria-hidden="true"></span>
                <?= $parent->likes ?> | <span class="glyphicon glyphicon-thumbs-down" style="color: red" aria-hidden="true"></span> <?= $parent->dislikes ?>
                <div class="review__buttons">
                    <button type="button" class="btn btn-info btn-sm" data-toggle="collapse" data-target="#main">Показать отзыв</button>
                    <a type="button" class="btn btn-info btn-sm" href="/chat/edit/<?= $parent->id ?>"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
                    <a type="button" class="btn btn-danger btn-sm" href="/chat/delete/<?= $parent->id ?>" data-confirm="Вы действительно хотите удалить отзыв id:<?= $parent->id ?>?"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>
                </div>
            </div>
            <div id="main" class="collapse review__comment">
                <?= $parent->text ?>
            </div>

            <?php
            showChildReviews($parent->id);
            ?>

        <?php } ?>
    </div>
</div>