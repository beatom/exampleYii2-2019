<?php
use common\service\Servis;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('app', 'Пополнить');
$first_system = null;
$first_withdraw = null;
$service = Servis::getInstance();

$withdraw_change_message = Yii::t('app', 'Для вывода средств перейдите в настройки и введите свои актуальные реквизиты');
$h = date('H');
if($h >= 15 OR $h < 10) {
    $withdraw_method_selected = false;
}
?>

<div class="content col pt-0">
    <div class="row deposit_block" style="<?= $stage == 1 ?: 'display:none;' ?>">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="payments">
                <div class="payments__link">
                    <a class="payments__link-item active" href="#"><?= Yii::t('app', 'Пополнить') ?></a>
                    <a class="payments__link-item" href="#"><?= Yii::t('app', 'Cнять') ?></a>
                </div>
                <div class="payments-items">
                    <?php
                    if (empty($systems)) {
                        echo 'Пополнение баланса в данный момент недоступно';
                    }
                    foreach ($systems as $key => $system) {
                        $add_class = '';
                        if ($system['id'] == $highlighted_system_id) {
                            $first_system = $system;
                            $add_class = 'active';
                        }
                        ?>
                        <div class="payments-item <?= $add_class ?>" data-system_id="<?= $key ?>">
                            <img src="<?= $system['image'] ?>">
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <?php if ($first_system) { ?>
            <div class="col-lg-5 pl-lg-1">
                <div class="deposit-payments-form">
                    <div class="deposit-payments-form__top">
                        <div class="deposit-payments-form__payment--item">
                            <img class="deposit-payments-form__payment" src="<?= $first_system['image'] ?>">
                        </div>
                        <div class="deposit-payments-form__deposit">Пополнение счета через<span
                                class="deposit-payments-form__deposit__via"><?= $first_system['title'] ?></span></div>
                    </div>

                    <?php
                    $balance_500 = $user->getBalance() > 200 ? true : false;
                    $form = ActiveForm::begin(['options' => ['class' => 'deposit-payments-form__amount nomarginbottom']]); ?>
                    <?= $balance_500 ? '<p>Для пополнения на балансе должно быть не больше 200$</p>' : $form->field($model, 'summ', [
                        'template' => '{label} <div class="input-group-append">{input}{error}<span>$</span></div>'
                    ])->textInput(['placeholder' => '', 'disabled' => $balance_500])

                    ?>
                    <label style="font-size: 13px;display: block;color: gray;text-align: center;margin-bottom: 25px;font-weight: 60;margin-top: 5px;">Она автоматически конвертируется в Вашу валюту</label>
                    <?= $form->field($model, 'system_id')->hiddenInput(['value' => $first_system['id']])->label(false); ?>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Пополнить'), ['class' => 'default-btn']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <div class="deposit-payments-form__info">
                        <div class="deposit-payments-form__info--item">Сумма пополнения (мин/макс):</div>
                        <div
                            class="deposit-payments-form__info--item__amount min_max"><?= $service->beautyDecimal($first_system['sum_min'], 0, '', ' ') ?> <?= $first_system['currency']['synonym'] ?>
                            / <?= $service->beautyDecimal($first_system['sum_max'], 0, '', ' ') ?> <?= $first_system['currency']['synonym'] ?></div>
                    </div>
                    <div class="deposit-payments-form__info">
                        <div class="deposit-payments-form__info--item">Комиссия:</div>
                        <div
                            class="deposit-payments-form__info--item__amount fee"><?= $service->beautyDecimal($first_system['fee']) ?>
                            %
                        </div>
                    </div>

                </div>
            </div>
        <?php } ?>
    </div>
    <div class="row deposit_block" style="<?= $stage == 2 ?: 'display:none;' ?>">
        <div class="col-lg-7 mb-4 mb-lg-0">
            <div class="payments">
                <div class="payments__link">
                    <a class="payments__link-item" href="#"><?= Yii::t('app', 'Пополнить') ?></a>
                    <a class="payments__link-item active" href="#"><?= Yii::t('app', 'Cнять') ?></a>
                </div>
                <div class="payments-items  withdraws">
                    <?php
                    if (empty($withdraws)) {
                        echo 'Вывод средств в данный момент недоступен';
                    }
                    foreach ($withdraws as $key => $system) {
                        $add_class = '';
                        if ($outModel->type) {
                            if ($system['id'] == $outModel->type) {
                                $first_withdraw = $system;
                                $add_class = 'active';
                            }
                        } elseif (!$first_withdraw) {
                            $first_withdraw = $system;
                            $add_class = 'active';
                        }
                        ?>
                        <div class="payments-item <?= $add_class ?>" data-withdraw_id="<?= $key ?>">
                            <img src="<?= $system['image'] ?>">
                        </div>
                    <?php } ?>

                </div>
                <p style="color: red;text-align: center;margin-top: 50px">Вывод средств ежедневно с 10-00 и до 15-00 по МСК. Срок вывода до 24-х часов.</p>
            </div>
        </div>
        <?php
        $first_withdraw = \common\models\PaymentSystemsWithdraw::findIdentity($first_withdraw['id']);
        $currency = $first_withdraw->currency(); ?>
        <div class="col-lg-5 pl-lg-1">
            <div class="deposit-payments-form">
                <div class="deposit-payments-form__top">
                    <div class="deposit-payments-form__payment--item">
                        <img class="withdraw-payments-form__payment" src="<?= $first_withdraw->image ?>">
                    </div>
                    <div class="deposit-payments-form__deposit">Вывод средств на <span
                            class="withdraw-payments-form__deposit__via"><?= $first_withdraw->title ?></span></div>
                </div>

                <?php
                if ($withdraw_method_selected) {
                    ?>
                    <div class="selected_withdraw">
                        <?php $form = ActiveForm::begin(['options' => ['class' => 'deposit-payments-form__amount']]); ?>
                        <?= $form->field($outModel, 'summ', ['template' => '{label} <div class="input-group-append">{input}{error}<span>$</span></div>'])->textInput(['placeholder' => '']) ?>
                        <?= $form->field($outModel, 'account_number', ['template' => '{label} <div class="input-group-append">{input}{error}</div>'])->textInput(['placeholder' => '', 'disabled' => true]) ?>
                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('app', 'Вывести'), ['class' => 'default-btn']) ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                        <div class="deposit-payments-form__info">
                            <div class="deposit-payments-form__info--item">Сумма вывода с учетом комиссии:</div>
                            <div class="deposit-payments-form__info--item__amount withdraw_fee">
                                0 $</div>
                        </div>

                        <div class="deposit-payments-form__info">
                            <div class="deposit-payments-form__info--item">Сумма вывода:</div>
                            <div class="deposit-payments-form__info--item__amount">от <?= $first_withdraw->sum_min ?>$
                            </div>
                        </div>
                        <div class="deposit-payments-form__info">
                            <div class="deposit-payments-form__info--item">Комиссия:</div>
                            <div
                                class="deposit-payments-form__info--item__amount"><?= $service->beautyDecimal($first_withdraw->fee) ?> %
                            </div>
                        </div>
                        <div class="deposit-payments-form__info">
                            <div class="deposit-payments-form__info--item">Для верифицированных:</div>
                            <div
                                class="deposit-payments-form__info--item__amount"><?= $service->beautyDecimal($first_withdraw->fee_verified) ?> %
                            </div>
                        </div>
                    </div>
                <?php }
                ?>
                <div class="default_withdraw" style="<?= $withdraw_method_selected ? 'display:none' : null ?>">
                    <?php $form = ActiveForm::begin(['method' => 'GET', 'action' => '/user/settings#settings-withdrawal', 'options' => ['class' => 'deposit-payments-form__amount']]); ?>
                    <?php if($h < 10 OR $h >= 15) { ?>
                    <div class="deposit-payments-form__info" style="margin-bottom: 20px;">
                        <?= Yii::t('app', 'Вывод средств доступен с 10:00 до 15:00 по МСК') ?>
                    </div>
                    <?php } ?>
                    <div class="deposit-payments-form__info" style="margin-bottom: 20px;">
                        <?= $withdraw_change_message ?>
                    </div>
                    <div class="form-group">
                        <?= Html::submitButton(Yii::t('app', 'Настройки'), ['class' => 'default-btn']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                    <div class="deposit-payments-form__info">
                        <div class="deposit-payments-form__info--item">Сумма вывода:</div>
                        <div class="deposit-payments-form__info--item__amount withdraw_sum_min">от <?= $first_withdraw->sum_min ?>$
                        </div>
                    </div>
                    <div class="deposit-payments-form__info">
                        <div class="deposit-payments-form__info--item">Комиссия:</div>
                        <div
                            class="deposit-payments-form__info--item__amount withdraw_fee_show"><?= $service->beautyDecimal($first_withdraw->fee) ?> %
                        </div>
                    </div>
                    <div class="deposit-payments-form__info">
                        <div class="deposit-payments-form__info--item">Для верифицированных:</div>
                        <div
                            class="deposit-payments-form__info--item__amount withdraw_fee_verified"><?= $service->beautyDecimal($first_withdraw->fee_verified) ?> %
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    var payment_systems = <?php print_r(json_encode($systems)) ?>;
    var withdraw_systems = <?php print_r(json_encode($withdraws)) ?>;
    var selected_withdraw_method = <?= $withdraw_method_selected ? $outModel->type : 'false' ?>;
    var user_verified = <?= $user->verified ? 'true' : 'false' ?>;
</script>


<?php
echo $send_form;
if ($send_form) {
    echo '<script> document.getElementById("payerrcash").submit() </script>';
}
?>


