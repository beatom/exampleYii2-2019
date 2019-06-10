<?php

// подключаем виджет постраничной разбивки
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\FileInput;
use yii\helpers\Url;
$protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';

$this->title = 'Карточка менеджера';

$plugin_options = [
    'previewFileType' => 'any',
    'uploadUrl' => Url::to(['/site/file-upload']),
];
if(isset($model->avatar)) {
    $plugin_options['initialPreview'] = [ $protocol . Yii::$app->params['frontendDomen'] . $model->avatar ];
    $plugin_options['initialPreviewAsData'] = true;
}

?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

    <h1><?= $this->title ?></h1>


    <div class="body-content">

        <div class="form-group">
            <?php $form = ActiveForm::begin(['id' => 'form-manager-card', 'options'=>['enctype'=>'multipart/form-data']]);?>

            <?= $form->field($model, 'name')->input('text')->label('Средняя скорость ответа поддержки')->label('Отображаемое имя менеджера')  ?>
            <?= $form->field($model, 'phone')->input('text', ['placeholder' => '+7 (977) 000-14-58'])->label('Телефон') ?>
            <?= $form->field($model, 'email')->input('text')->label('E-mail') ?>
            <?= $form->field($model, 'position')->input('text')->label('Должность') ?>
<!--            --><?//= isset($model->avatar) ? "<img src='".$protocol . Yii::$app->params['frontendDomen'] . $model->avatar."' style='max-height: 150px;'>": null?>

            <label>Фото менеджера</label>

            <?= FileInput::widget([
            'name' => 'ManagerCard[avatar]',
            'language' => 'ru',
            'options' => ['multiple' => false, 'accept' => 'image/*'],
            'pluginOptions' => $plugin_options
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>
</div>
