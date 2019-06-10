<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = $seo['title'];
$this->params['breadcrumbs'][] = [  'label' => 'Шаблоны писем', 'url' => ['/email_template']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-sms']); ?>

            <?= $form->field($model, 'synonym')->input('text')->label('Название') ?>
            <?= $form->field($model, 'title')->input('text')->label('title') ?>

            <?= $form->field($model, 'text')->widget(Widget::classname(), [
                'settings' => [
                    'lang' => 'ru',
                    'minHeight' => 300,
                    'pastePlainText' => false,
                    'cleanSpaces' => false,
                    'replaceDivs' => false,
                    'cleanOnPaste' => false,
                    'buttonSource' => true,
                    'plugins' => [
                        'clips',
                        'fullscreen'
                    ],
                    'imageUpload' => Url::to(['/page/image-upload']),
                    'imageManagerJson' => Url::to(['/page/images-get']),
                    'fileManagerJson' => Url::to(['/page/files-get']),
                    'fileUpload' => Url::to(['/page/file-upload'])
                ]
            ]);?>
            <span><?= $model->comment ?></span>
            <br><br><br>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
