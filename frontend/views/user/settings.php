<?php

use kartik\widgets\ActiveForm;
use common\models\BalanceLog;
use common\models\PaymentSystemsWithdraw;
for ($y = (date('Y') - 17); $y >= 1920; $y--) {
    $yearBirth[$y] = $y;
}
for ($d = 01; $d <= 31; $d++) {
    $day = ($d < 10) ? '0' . $d : $d;
    $dayBirth[$day] = $d;
}
foreach ($countries as $country) {
    $c[$country->id] = $country->name;
}
$monthbirth = ['01' => 'Января', '02' => 'Февраля', '03' => 'Марта', '04' => 'Апреля', '05' => 'Мая', '06' => 'Июня', '07' => 'Июля', '08' => 'Августа', '09' => 'Сентября', '10' => 'Октября', '11' => 'Ноября', '12' => 'Декабря',];
if (\Yii::$app->language != 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
    $monthbirth = ['01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December',];

}
$this->title = Yii::t('cab', 'Управление аккаунтом');
?>

<div class="content col pt-0">
    <div class="settings">
        <?php if ($showmsg) { ?>
            <div class="notification_message">
                <?= $showmsg ?>
            </div>
        <?php
        }
        if($secuity->step == 1) {
        ?>
        <div class="settings-account">

            <h4 class="settings-account__title">Мои данные</h4>
            <?php $form = ActiveForm::begin(['id' => 'formInfo', 'options' => ['class' => 'settings-account__form', 'enctype' => 'multipart/form-data']]); ?>
            <div class="input-group">
                <?= $form->field($model, 'avatar', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'template' => '{label}<div class="fileUpload"><div class="control-label">Загрузить файл</div>{input}{error}<input class="file-loader fakeUploadLogo" placeholder="Файл не выбран" disabled="disabled"></div>',
                    'labelOptions' => ['class' => '']
                ])
                    ->fileInput(['id' => 'logo-id', 'class' => 'attachment_upload', 'accept' => '.png, .jpg, .jpeg'])
                    ->label(Yii::t('cab', 'Аватар')) ?>

                <?= $form->field($model, 'username', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', [
                        'value' => $user->username,
                        'placeholder' => Yii::t('cab', 'Введите имя'),
                        'disabled' => true
                    ])
                    ->label(Yii::t('cab', 'Логин')) ?>
            </div>
            <p class="after_upload_message">Не забудьте чуть ниже нажать “Сохранить”</p>

            <div class="input-group">

                <?= $form->field($model, 'firstname', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', [
                        'value' => $user->firstname,
                        'placeholder' => Yii::t('cab', 'Введите имя'),
                        'disabled' => $user->firstname ? true : false
                    ])
                    ->label(Yii::t('cab', 'Имя')) ?>

                <?= $form->field($model, 'country_id', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']

                ])
                    ->dropDownList($c, ['disabled' => ($user->country_id != null ) ? true : false])->label(Yii::t('cab', 'Страна')) ?>

            </div>
            <div class="input-group">
                <?= $form->field($model, 'lastname', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', [
                        'value' => $user->lastname,
                        'placeholder' => Yii::t('cab', 'Введите фамилию'),
                        'disabled' => $user->lastname ? true : false
                    ])
                    ->label(Yii::t('cab', 'Фамилия')) ?>

                <?= $form->field($model, 'city_name', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', [
                        'value' => $user->city_name,
                        'placeholder' => Yii::t('cab', 'Выберите город'),
                        'disabled' => $user->city_name ? true : false
                    ])
                    ->label(Yii::t('cab', 'Город')) ?>

            </div>
            <div class="input-group">
                <?= $form->field($model, 'middlename', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', [
                        'value' => $user->middlename,
                        'placeholder' => Yii::t('cab', 'Введите отчество'),
                        'disabled' => $user->middlename ? true : false
                    ])
                    ->label(Yii::t('cab', 'Отчество')) ?>

                <?= $form->field($model, 'phone', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->input('tel', [
                        'value' => $user->phone,
                        'placeholder' => '+7XXXXXXXXXX',
                        //'disabled' => $user->phone ? true : false
                    ])
                    ->label(Yii::t('cab', 'Мой номер')) ?>
                <div id="change_phone_1" class="form-group" style="display: none">
                    <div class="change-phone">Изменить</div>
                </div>

                
                <div id="change_phone_2" class="form-group" style="display: none">
                    <?= $form->field($model, 'sms_code', [
                        'options' => ['class' => 'col-lg-6'],
                        'template' => '{input}{error}',
                        'labelOptions' => ['class' => '']
                    ])
                        ->textInput(['class' => 'form-control sms-code', 'placeholder' => 'Введите смс код', 'value' => 1]) ?>
                    <div class="col-lg-6">
                        <button class="settings-account__btn mb-2">Подтвердить</button>
                    </div>
                </div>
                <div id="change_phone_3" class="form-group" style="display: none">
                    Телефон успешно изменен
                </div>
            </div>
            <div class="input-group">
                <button class="settings-account__btn" type="submit">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
        <div class="settings-verification">
            <h4 class="settings-account__title">Верификация</h4>
            <?php $form = ActiveForm::begin(['id' => 'formDocument', 'options' => ['class' => 'settings-account__form', 'enctype' => 'multipart/form-data']]); ?>
            <div class="input-group">
                <?= $form->field($model, 'pasport_1', [

                    'options' => ['class' => 'form-group col px-0 max-w-auto'],
                    'template' => '{label}<div class="fileUpload"><div class="control-label">Загрузить файл</div>{input}{error}<input class="file-loader fakeUploadLogo" placeholder="Файл не выбран" disabled="disabled"></div>',
                    'labelOptions' => ['class' => '']
                ])
                    ->fileInput([
                        'id' => 'logo-id',
                        'class' => 'attachment_upload',
                        'accept' => '.png, .jpg, .jpeg',
                    ])
                    ->label('Документ') ?>

            </div>
            <p class="after_upload_message">Не забудьте чуть ниже нажать “Отправить”</p>
            <div class="input-group">
                <div class="info">
                    <p>Скорее проходи верификацию. Для верифицированных у нас сниженные комиссии как на ввод, так и
                        на вывод! Загрузите фотографию разворота паспорта в руках рядом с лицом, на фоне должен быть
                        сайт компании invest.</p>
                </div>
            </div>
            <div class="input-group">
                <button class="settings-account__btn" type="submit">Отправить</button>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
        <div class="settings-social">
            <h4 class="settings-account__title">Мои социальные сети</h4>
            <?php $form = ActiveForm::begin(['id' => 'formPartner', 'options' => ['class' => 'settings-account__form']]); ?>
            <div class="input-group">
                <?= $form->field($model, 'vk', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'template' => '{label}{input}{error}<div class="input-group-append">
                        <div class="vk icons"></div>
                    </div>',
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', ['value' => ($social->vk) ? htmlspecialchars_decode($social->vk) : '', 'placeholder' => 'https://vk.com/xxxxxx'])
                    ->label('VK') ?>


                <?= $form->field($model, 'facebook', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'template' => '{label}{input}{error}<div class="input-group-append">
                        <div class="facebook icons"></div>
                    </div>',
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', ['value' => ($social->facebook) ? htmlspecialchars_decode($social->facebook) : '', 'placeholder' => 'https://facebook.com/xxxxxx'])
                    ->label('Facebook') ?>

            </div>
            <div class="input-group">
                <?= $form->field($model, 'telegram', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'template' => '{label}{input}{error}<div class="input-group-append">
                        <div class="telegram icons"></div>
                    </div>',
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', ['value' => ($social->telegram) ? htmlspecialchars_decode($social->telegram) : '', 'placeholder' => '@xxxxxx'])
                    ->label('Telegram') ?>

                <?= $form->field($model, 'instagram', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'template' => '{label}{input}{error}<div class="input-group-append">
                        <div class="instagram icons"></div>
                    </div>',
                    'labelOptions' => ['class' => '']
                ])
                    ->input('text', ['value' => ($social->instagram) ? htmlspecialchars_decode($social->instagram) : '', 'placeholder' => 'https://instagram.com/xxxxxx'])
                    ->label('Instagram') ?>
            </div>
            <div class="input-group">
                <div class="info">
                    <p>
                        Они отображены в клиентском чате. Чем больше вы будете писать в чате, тем чаще к вам будут
                        обращаться потенциальные клиенты и регистрироваться как ваши приглашенные!</p>
                </div>
            </div>
            <div class="input-group">
                <button class="settings-account__btn" type="submit">Сохранить</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>

        <div id="settings-withdrawal" class="settings-withdrawal">
            <h4 class="settings-account__title">Реквизиты для вывода</h4>
            <?php  $form = ActiveForm::begin(['id' => 'security-form',
                'options' => ['class' => 'settings-account__form']
            ]); ?>
            <div class="input-group">
                <div class="form-group col-lg-4 px-0">
                    <div class="settings-withdrawal__item">
                        <div class="settings-withdrawal__item--title"><?= ($user->payment_system AND $user__payment_system = PaymentSystemsWithdraw::findIdentity($user->payment_system)) ? $user__payment_system->title : 'Выберите платёжную систему' ?></div>
                        <ul class="settings-withdrawal__item--list">
                            <?php
                            $items = PaymentSystemsWithdraw::getSystems();
                            foreach ($items as $key => $item) {
                            ?>
                                <li>
                                    <a class="settings-withdrawal__item--list__item <?= $item->id == $user->payment_system ? 'active' : null ?>" data-system_id="<?= $item->id  ?>" href="#"><?= $item->title ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>


                <?= $form->field($model, 'payment_address', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'template' => '{input}{error}',
                    'labelOptions' => ['class' => '']
                ])->textInput(['class' => 'form-control', 'placeholder' => 'Введите реквизиты']) ?>

                <div id="payment_step1" class="form-group" style="display: none">
                    <button class="settings-account__btn mb-2">Получить смс код</button>
                </div>
                <div id="payment_step2" class="form-group" style="display: none">
                    <?= $form->field($model, 'sms_code', [
                        'options' => ['class' => 'col-lg-6'],
                        'template' => '{input}{error}',
                        'labelOptions' => ['class' => '']
                    ])
                        ->textInput(['class' => 'form-control sms-code', 'placeholder' => 'Введите смс код']) ?>
                    <div class="col-lg-6">
                        <button class="settings-account__btn mb-2">Подтвердить</button>
                    </div>
                </div>
                <div id="payment_step3" class="form-group" style="display: none">
                    Платежные реквизиты успешно изменены
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="settings-password <?= $secuity->hasErrors() ? 'gototag' : null ?>">
            <h4 class="settings-account__title">Пароль для входа</h4>
            <?php
            $form = ActiveForm::begin(['id' => 'security-form',
                'options' => ['class' => 'settings-account__form']
            ]); ?>
            <div class="input-group">
                <?= $form->field($secuity, 'oldpass', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->passwordInput([
                        'value' => '',
                        'placeholder' => Yii::t('cab', 'Введите старый пароль'),
                    ])
                    ->label(Yii::t('cab', 'Старый')) ?>
            </div>
            <div class="input-group">
                <?= $form->field($secuity, 'newpass1', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->passwordInput([
                        'value' => '',
                        'placeholder' => Yii::t('cab', 'Введите новый пароль'),
                    ])
                    ->label(Yii::t('cab', 'Новый')) ?>

            </div>
            <div class="input-group">
                <?= $form->field($secuity, 'newpass2', [
                    'options' => ['class' => 'form-group col-lg-4 px-0'],
                    'labelOptions' => ['class' => '']
                ])
                    ->passwordInput([
                        'value' => '',
                        'placeholder' => Yii::t('cab', 'Подтвердите новый пароль'),
                    ])
                    ->label(Yii::t('cab', 'Подтвердите')) ?>
            </div>
            <div style="display:none;">
                <?= $form->field($secuity, 'sms_code')->hiddenInput(['value' => '111111'])->label(false)  ?>
                <?= $form->field($secuity, 'step')->hiddenInput()->label(false) ?>
            </div>
            <div class="input-group">
                <button class="settings-account__btn" type="submit">Обновить</button>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <?php } else { ?>
            <div class="settings-password">
                <h4 class="settings-account__title">Подтвердите изменения пароля</h4>
                <?php
                $form = ActiveForm::begin(['id' => 'security-form',
                    'options' => ['class' => 'settings-account__form']
                ]); ?>
                <div class="input-group">
                    <?= $form->field($secuity, 'sms_code', [
                        'options' => ['class' => 'form-group col-lg-4 px-0', 'style' => 'max-width:unset'],
                        'labelOptions' => ['class' => '']
                    ])
                        ->textInput([
                            'value' => '',
                            'placeholder' => Yii::t('cab', 'Код из смс'),
                        ])
                        ->label(Yii::t('cab', 'Введите смс код отправленный, на Ваш номер телефона')) ?>
                </div>
                <div style="display:none;">
                <?= $form->field($secuity, 'newpass1')->hiddenInput()->label(false) ?>
                <?= $form->field($secuity, 'newpass2')->hiddenInput()->label(false) ?>
                <?= $form->field($secuity, 'oldpass')->hiddenInput()->label(false) ?>
                <?= $form->field($secuity, 'step')->hiddenInput()->label(false) ?>
                </div>
                <div class="input-group">
                    <button class="settings-account__btn" type="submit">Отправить</button>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        <?php } ?>
    </div>
</div>