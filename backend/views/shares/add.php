<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use vova07\imperavi\Widget;
use yii\helpers\Url;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = $seo['title'];
$this->params['breadcrumbs'][] = [  'label' => 'Акции', 'url' => ['/shares/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-add-news','options' => ['enctype' => 'multipart/form-data']]);

            if ($model->img){
                echo '<img width="133px" src="//'.$seo['frontend_domen'].$model->img.'">';
            } ?>

            <?= $form->field($model, 'img')->input('file')->label('IMG миниатюра') ?>

            <?= $form->field($model, 'date_start')->input('date')->label('Дата начала') ?>
            <?= $form->field($model, 'date_end')->input('date')->label('Дата окончания') ?>

            <?= $form->field($model, 'synonym')->input('text')->label('ЧПУ. Только английские буквы и цыфры без пробелов') ?>

            <?= $form->field($model, 'title')->input('text')->label('Title') ?>
            <?= $form->field($model, 'title_en')->input('text')->label('Title EN') ?>
            <?= $form->field($model, 'text_small')->input('text')->label('Анотация. Краткий текст') ?>
            <?= $form->field($model, 'text_small_en')->input('text')->label('Анотация. Краткий текст EN') ?>

            <?= $form->field($model, 'text_big')->widget(Widget::classname(), [
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

            <?= $form->field($model, 'text_big_en')->widget(Widget::classname(), [
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

            <?= $form->field($model, 'meta_title')->input('text')->label('SEO title') ?>
            <?= $form->field($model, 'meta_description')->input('text')->label('SEO description') ?>
            <?= $form->field($model, 'meta_keyword')->input('text')->label('SEO keyword') ?>

            <?= $form->field($model, 'status')->dropDownList([1=>'включено', 0=>'выключено'])->label('Статус') ?>

            <div class="form-group">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
