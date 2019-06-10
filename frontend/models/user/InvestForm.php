<?php
namespace frontend\models\user;

use common\models\Currencies;
use common\models\PaymentSystems;
use yii\base\Model;


class InvestForm extends Model
{
    public $summ;
    public $system_id;
    public $summ_usd;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['summ', 'system_id'], 'required', 'message' => 'Введите сумму'],
            ['summ', 'double', 'message' => 'Целое число не меньше 5', 'min' => 5, 'tooSmall' => 'Целое число не меньше 10'],

            ['system_id', 'integer', 'min' => 1],
            ['system_id', 'exist', 'targetClass' => PaymentSystems::class, 'targetAttribute' => 'id', 'message' => 'Системы с данным id не существует'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'summ' => 'Введите сумму в долларах',
        ];
    }

    public function deposit()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = \Yii::$app->user->identity;
        if (!$payment_system = PaymentSystems::find()->where(['id' => $this->system_id, 'show' => 1])->with('currency')->one()) {
            return false;
        }
        if ($user->getBalance() > 200) {
            $this->addError('summ', 'Для пополнения на балансе должно быть не больше 200$');
            return false;
        }

        $this->summ_usd = $this->summ;
        if ($payment_system->currency_id != 1) {
            $this->summ_usd = $this->summ_usd * Currencies::getRate('USD') / $payment_system->currency->value;
        }

        if ($this->summ_usd < $payment_system->sum_min) {
            $error_message = \Yii::t('app', 'Минимум') . ' ' . $payment_system->sum_min . ' ' . $payment_system->currency->synonym;
            $this->addError('summ', $error_message);
            return false;
        } elseif ($this->summ_usd > $payment_system->sum_max) {
            $error_message = \Yii::t('app', 'Maксимум') . ' ' . $payment_system->sum_max . ' ' . $payment_system->currency->synonym;
            $this->addError('summ', $error_message);
            return false;
        } elseif ($payment_system->system == 'PayinPayout' AND !$user->phone) {
            $error_message = \Yii::t('app', 'Укажите номер телефона в настройках');
            $this->addError('summ', $error_message);
            return false;
        }

        $str = 'common\service\api_terminal\\' . $payment_system->system;
        $payment_provider = $str::getInstance();
        $res = $payment_provider->getForm($user, $this->summ_usd + $this->summ_usd * ($payment_system->fee_add / 100), $payment_system->via, $this->summ);
        return $res;
    }

}
