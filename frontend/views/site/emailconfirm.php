<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Войти';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="items-wrapper__title">
    <div class="b-h2">Подтверждение email</div>
    <h5 class="items-wrapper__description">Ваш email успешно подтвержден</h5>
</div>
<div class="items-wrapper__form">
    <form method="GET" action="/user/my-info">
        <div class="d-flex justify-content-between items-wrapper__form__links">
            <button class="c-btn is-bg-color-black items-wrapper__form__links--item" type="submit">Мои настройки</button>
        </div>
    </form>
</div>
