<?php

// подключаем виджет постраничной разбивки
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use vova07\imperavi\Widget;

$this->title = 'Настройки блоков';
?>

<div class="site-index">

    <?= $this->render('/seting/menu'); ?>

        <h1><?= $this->title ?></h1>

    <div class="body-content">

        <?php
        if($is_save){?>
            <p class="alert bg-success">Настройки сохранены</p><?php
        } ?>

        <?php $form = ActiveForm::begin(['id' => 'form-blocks','options' => ['enctype' => 'multipart/form-data']]);?>

        <div class="form-group">
            <?= $form->field($model, 'plan_trust_management')->widget(Widget::classname(), [
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
            ])->label('Блок "Доверительное управление" вводится на главной в блоке "представляем" <br>и в блоке "Альтернативные предложения" на странице инвестиционного предложения');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'plan_bonds')->widget(Widget::classname(), [
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
            ])->label('Блок "Облигации" вводится на главной в блоке "представляем" <br>и в блоке "Альтернативные предложения" на странице инвестиционного предложения');?>
        </div>

        <div class="form-group">
            <?= $form->field($model, 'plan_solution')->widget(Widget::classname(), [
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
            ])->label('Блок "Готовое решение" вводится на главной в блоке "представляем" <br>и в блоке "Альтернативные предложения" на странице инвестиционного предложения');?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
        </div>

        <?php ActiveForm::end(); ?>


    </div>
</div>
