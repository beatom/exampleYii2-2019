<?php

use common\models\trade\TradingAccount;
use yii\widgets\LinkPager;
use yii\helpers\Url;
use common\service\Servis;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

if(\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}
$this->title = 'Расчеты за ' . $model->date_add;

$service = Servis::getInstance();

$this->params['breadcrumbs'][] = $this->title;
?>
<h3><?= $this->title ?></h3>
<div class="row">
    <div class="col-lg-12">
        <?= $this->render('_menu') ?>

        <?= $report ?>
    </div>
</div>
