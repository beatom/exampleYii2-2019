<?php

// подключаем виджет постраничной разбивки
use yii\widgets\LinkPager;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\service\Servis;
use kartik\widgets\DatePicker;

$this->title = 'Редактирование пользователя';
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['/user/index']];
$this->params['breadcrumbs'][] = $this->title;

foreach ($countries as $country) {
    $c[$country->id] = $country->name;
}

$monthbirth = ['01' => 'Января', '02' => 'Февраля', '03' => 'Марта', '04' => 'Апреля', '05' => 'Мая', '06' => 'Июня', '07' => 'Июля', '08' => 'Августа', '09' => 'Сентября', '10' => 'Октября', '11' => 'Ноября', '12' => 'Декабря',];

$roles_names = \common\models\User::$roles_names;
$roles = [];
foreach (Yii::$app->authManager->getRoles() as $key => $value) {
    if($key != 'admin') {
        $roles[$key] = $roles_names[$key];
    }
}

?>

<h3><?= $user->username ?> <?= $user->vip ? '<span style="color: green" class="glyphicon glyphicon-ok" title="VIP клиент" aria-hidden="true"></span>' : null ?></h3>
<div class="row">

    <div class="col-lg-2">
        <?= $this->render('menu', ['user_id' => $user->id]) ?>
    </div>
    <div class="col-lg-10">

        <?php $form = ActiveForm::begin(['id' => 'formInfo', 'options' => ['class' => 'form form-info', 'enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($model, 'username')->input('text')->label('Ник') ?>
        <?= $form->field($model, 'phone')->input('text', ['placeholder' => 'Введите номер'])->label('Телефон') ?>
        <?= $form->field($model, 'email')->input('text', ['placeholder' => 'example@gmail.com'])->label('Эл. почта') ?>

        <?= $form->field($model, 'firstname')->input('text', ['placeholder' => 'Введите имя'])->label('Имя') ?>
        <?= $form->field($model, 'middlename')->input('text', ['placeholder' => 'Введите отчество'])->label('Отчество') ?>
        <?= $form->field($model, 'lastname')->input('text', ['placeholder' => 'Введите фамилию'])->label('Фамилия') ?>

        <?php if ($model->avatar){
                echo '<img width="150px" src="//'.$seo['frontend_domen'].$model->avatar.'">';
            }
        ?>
        <?= $form->field($model, 'avatar')->input('file')->label('Аватар') ?>

        <label class="control-label">Дата рождения</label>
        <?php echo DatePicker::widget([
            'model' => $model,
            'attribute' => 'date_birthday',
            'options' => ['placeholder' => 'Выберите дату рождения пользователя'],
            'language' => 'ru',
            'pluginOptions' => [
                'autoclose' => true,
                'toggleActive' => true,
                'format' => 'yyyy-mm-dd',
                'endDate' => "0d"
            ]
        ]); ?>
        <?= $form->field($model, 'country_id')->dropDownList($c)->label('Страна') ?>
        <?= $form->field($model, 'city_name')->input('text', ['placeholder' => 'Город'])->label('Город') ?>
        <hr>
        <h4>Социальные сети</h4>
        <?= $form->field($model, 'vk')->input('text', ['placeholder' => 'https://vk.com/id22539750'])->label('Я в VK') ?>
        <?= $form->field($model, 'facebook')->input('text', ['placeholder' => 'https://facebook.com/yezhowa'])->label('Я в FB') ?>
        <?= $form->field($model, 'instagram')->input('text', ['placeholder' => 'https://instagram.com/yezhowa'])->label('Я в Instagram') ?>
        <?= $form->field($model, 'skype')->input('text', ['placeholder' => 'skype'])->label('Я в Skype') ?>
        <?= $form->field($model, 'whatsapp')->input('text', ['placeholder' => 'whatsapp'])->label('Я в Whatsapp') ?>
        <?= $form->field($model, 'telegram')->input('text', ['placeholder' => 'telegram'])->label('Я в telegram') ?>
        <hr>
        <?= $form->field($model, 'manager_id')->dropDownList($managers)->label('Личный менеджер') ?>
        <?= $form->field($model, 'vip')->checkbox(['checked' => $model->vip ? true : false])->label('VIP пользователь') ?>
        <hr>
        <?= $form->field($model, 'role')->checkboxList($roles)->label('Роли пользователя') ?>
        <hr>
        <?= $form->field($model, 'promo_code')->textInput()->label('Промо код') ?>
        <?= $form->field($model, 'invitation_code')->textInput()->label('Пригласительная ссылка') ?>

        <?= Html::submitButton('Сохранить', ['class' => 'u-link-action', 'name' => 'mydata', 'type' => 'submit']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
