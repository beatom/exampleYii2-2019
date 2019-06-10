<?php
use yii\helpers\Url;

$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
?>

<div style="margin-bottom: 30px;">
    <h3>Основная страница</h3>
    <a href="<?= $protocol . Yii::$app->params['frontendDomen'] . $model->pasport_1 ?>" target="_blank">
        <img style="max-height: 300px; width: auto;" src="<?= $protocol . Yii::$app->params['frontendDomen'] . $model->pasport_1 ?>">
    </a>
    <h3>Страница с пропиской</h3>
    <a href="<?= $protocol . Yii::$app->params['frontendDomen'] . $model->pasport_2 ?>" target="_blank">
        <img style="max-height: 300px; width: auto;" src="<?= $protocol . Yii::$app->params['frontendDomen'] . $model->pasport_2 ?>">
    </a>
</div>

<div class="form-group">
    <a class="btn btn-primary" href="<?= Url::to('/user/identify/'.$model->id) ?>">Подтвердить</a>
</div>