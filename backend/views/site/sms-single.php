<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;

//['value' => ($user->firstname)? $user->firstname: '']
$this->title = $seo['title'];
$this->params['breadcrumbs'][] = [  'label' => 'SMS шаблоны', 'url' => ['/sms_template']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin(['id' => 'form-sms']); ?>

            <?= $form->field($model, 'synonym')->input('text')->label('Название') ?>
            <?= $form->field($model, 'text')->textarea(['rows'=>5])->label('Текст') ?>
            <span> <?php echo $model->comment; ?></span>
            <br><br><br>

            <div class="form-group">
                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary', 'name' => 'mydata']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
