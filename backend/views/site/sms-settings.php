<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use yii\widgets\LinkPager;
use yii\helpers\Url;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = 'Настройки смс провайдеров';
$this->params['breadcrumbs'][] = [  'label' => 'Настройки', 'url' => ['/seting/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="body-content">
    

        <table class="table">

            <tr>
                <th>Название</th>
                <th>Логин</th>
                <th>Пароль</th>
                <th>Статус</th>
                <th>Действия</th>
                <th></th>
            </tr>

            <?php foreach ($models as $model) { ?>
                <tr>
                <td> <?= $model->name ?></td>
                <td> <?= $model->api_login ?></td>
                <td> <?= $model->api_password ?></td>
                <td> 
                <?php if($model->is_active) { ?>
                <span>Активен</span>
                <td><a href="<?= Url::to(['/site/deactive_sms_provider', 'id' => $model->id ]) ?>">Деактивировать</a>
                <?php } else { ?>
                    <span>-</span>
                    <td><a href="<?= Url::to(['/site/active_sms_provider', 'id' => $model->id ]) ?>">Активировать</a>
                    <?php
            }?>
                </td>
                <td> <a href="<?= Url::to(['/site/edit_sms_provider', 'id' => $model->id ]) ?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
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