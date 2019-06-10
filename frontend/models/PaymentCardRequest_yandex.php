<?php
namespace frontend\models;

use yii\db\ActiveRecord;
use Yii;
use common\models\QueueMail;
use common\models\User;
use common\models\Overdraft;
use common\models\BonusDebt;
use common\models\PaymentCardRequest;


class PaymentCardRequest_yandex extends PaymentCardRequest
{

    public function rules()
    {
        return [
            [['user_id', 'card_number', 'summ_rub', 'summ_usd'], 'required', 'message' => 'Необходимо заполнить'],
            ['summ_usd', 'double', 'min' => 10],
            ['card_number', 'string', 'min' => 10, 'max' => 15, 'tooShort' => 'Номер должен быть от 10 до 16 символов'],
            //['card_number', 'match', 'pattern' => '/^[0-9]+$/i', 'message' => 'Номер карты должен состоять только из чисел'],
        ];
    }


}
