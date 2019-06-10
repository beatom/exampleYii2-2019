<?php

namespace common\service\api_terminal;

use common\service\Servis;
use yii\helpers\Url;
use common\models\PaymentLog;
use common\models\BalanceLog;
use common\models\Currencies;

class Piastrix
{
    private static $instance = null;
    public $merchant_id = '';
    public $secret = '';

    private static $currencies = [
        'RUR' => 643,
        'USD' => 840,
        'EUR' => 978,
        'UAH' => 980
    ];

    private static $via_currencies = [
        'card_uah' => 'UAH',
        'privat24_uah' => 'UAH',
        'btc_usd' => 'USD',
        'qiwi_eur' => 'EUR',
        'qiwi_usd' => 'EUR',
    ];

    private static $via_payways = [
        'card_rub' => 4,
        'card_uah' => 4,
        'qiwi_rub' => 2,
        'qiwi_usd' => 2,
        'qiwi_eur' => 2,
        'yamoney_rub' =>  1,
        'alfaclick_rub' => 5,
        'privat24_uah' => 6,
        'btc_usd' => 7,
    ];

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
            self::$instance->merchant_id = \Yii::$app->params['piastrix_merchant_id'];
            self::$instance->secret = \Yii::$app->params['piastrix_secret'];
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}




    public function getForm( $user, $summ, $via) {


        if(!$order = PaymentLog::addLog($user->id, $summ, BalanceLog::piastrix, static::$via_payways[$via])) {
            return false;
        };
        $mess = 'Пополнение счета invest';
        



        if(isset(static::$via_currencies[$via])){
            $currency_name = static::$via_currencies[$via];
            if($currency_name == 'EUR') {
                $summ =number_format(Currencies::getRate('USD') * $summ / Currencies::getRate('EUR'), 2, '.', '');
            } elseif ($currency_name == 'UAH') {
                $summ =number_format(Currencies::getRate('USD') * $summ * 10 / Currencies::getRate('UAH'), 2, '.', '');
            } else {
                $summ = Servis::getInstance()->beautyDecimal($summ, 2, '.', '');
            }

            $currency = static::$currencies[$currency_name];
        } else {
            $currency = 643;
            $summ = number_format(Currencies::getRate('USD') * $summ, 2, '.', '');
        }
        //840;
        $keys_sorted = [$summ, $currency, $this->merchant_id, $order->id];
        $sign = hash('sha256', implode(':', $keys_sorted) . $this->secret);


        $out = '<form id="payerrcash" name="payment" method="post" action="https://pay.piastrix.com/ru/pay" enctype="utf-8" accept-charset="UTF-8">
                <input type="hidden" name="shop_id" value="'.$this->merchant_id.'" />
                <input type="hidden" name="shop_order_id" value="'.$order->id.'" />
                <input type="hidden" name="amount" value="'.$summ.'" />
                <input type="hidden" name="currency" value="'.$currency.'" />
                <input type="hidden" name="sign" value="'.$sign.'" />
                <input type="hidden" name="description" value="'.$mess.'" />
                <input type="hidden" name="payway" value="'.$via.'" />
                <input type="submit" style="display: none;" value="Pay">
                </form>';
        return $out;
    }

}
