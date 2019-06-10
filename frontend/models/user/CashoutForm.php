<?php
namespace frontend\models\user;

use common\models\LogConfirm;
use common\models\PaymentSystems;
use common\models\PaymentSystemsWithdraw;
use common\models\User;
use common\service\Servis;
use yii\base\Model;
use common\models\BalanceLog;
use common\models\EmailTemplate;
use Yii;
use common\models\SmsManager;
use common\models\Currencies;

class CashoutForm extends Model
{
    public $summ;
    public $type;
    public $account_number;
    public $cripto;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            [['summ','account_number'], 'trim'],
            [['account_number', 'type', 'cripto'], 'safe'],
            [['summ', 'account_number'], 'required', 'message'=> Yii::t('cab', 'Необходимо заполнить')],
            ['summ', 'match', 'pattern' => '/^[0-9]+(\.[0-9]{1,2})?$/', 'message' => 'Сумма с точностью до сотых']
        ];
        $user = Yii::$app->user->identity;
        $min_sum = 5;
        if($withdraw = PaymentSystemsWithdraw::findActive($user->payment_system)) {
            $min_sum = $withdraw->sum_min;
        }
        $rules[] = ['summ', 'double', 'message' => 'Дробное число с разделителем "."', 'min' => $min_sum, 'tooSmall' => Yii::t('cab', 'Значение должно быть не меньше '.$min_sum)];
        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'summ' => 'Введите сумму',
            'account_number' => 'Номер кошелька',
        ];
    }

    public function checkTransfer( $user )
    {
        if (!$this->validate()) {
            return null;
        }
        if(!$withdraw = PaymentSystemsWithdraw::findActive($this->type)) {
            return null;
        }
        if($withdraw->sum_min > $this->summ) {
            $this->addError('summ', Yii::t('cab', 'Минимальная сумма = '.$withdraw->sum_min.'$'));
            return null;
        }
        if( $user->balance < $this->summ ){
            $this->addError('summ', Yii::t('cab', 'У вас недостаточно средств'));
            return null;
        }

        $withdraw->fee = $user->verified ?  $withdraw->fee_verified :  $withdraw->fee;
        $summ_with_commision = !$user->vip ? Servis::getInstance()->beautyDecimal($this->summ * ((100 - $withdraw->fee)/100)) : $this->summ;

        $summ_rub = $withdraw->currency_id != 0 ? ' ('. number_format((Currencies::getRate('USD') * $summ_with_commision), 2, '.', ''). ' руб.)' : null;
        $comment = 'Вывод средств через '.BalanceLog::$system[$this->type].'. После подтверждения переведите сумму <b>'.$summ_with_commision. $summ_rub .'</b> вручную. Номер счета - '.$this->account_number;
//        if(!empty($this->cripto) && $this->type == BalanceLog::cryptonator){
//        	$comment .= '. Валюта - '. BalanceLog::$cripto_valute[$this->cripto];
//        }

        $balanse_log = new BalanceLog();
        $balanse_log->addLog($user->id, ($this->summ * (-1)), BalanceLog::exit_deposit, BalanceLog::in_processing, $this->type, null, $comment, null, 'withdraw_'.$this->account_number);
        if ($balanse_log->save()) {
                $user->balance -= $this->summ;
                $user->save();
             //   $email_template = EmailTemplate::findIdentity(EmailTemplate::BALANCE_BONUS_UP);
             //   $email_template->sendMail(Yii::$app->params['adminEmail'], 'Заявка на вывод средств',['text'=>$comment,'html'=>$comment]);
        }

        return true;
    }


}
