<?php
namespace frontend\models\user;

use yii\base\Model;
use common\models\User;
use common\models\LogConfirm;
use common\models\EmailTemplate;
use common\models\ChatTemplate;


/**
 * Signup form
 */
class SmsConfirmForm extends Model
{

    public $sms_code;



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sms_code', 'trim'],
            ['sms_code', 'required', 'message'=> 'Необходимо заполнить'],

        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function checkCode($user, $param)
    {
        if (!$this->validate()) {
            return null;
        }

        $phone = User::clearPhone( $user->phone );
        $confirm = LogConfirm::find()->orderBy(' date_add DESC')->where('phone = '.$phone.' AND date_add > '. (time() - 3600 ))->limit(1)->all();

        $confirm_phone = false;
        foreach ($confirm as $item ){
            if($this->sms_code == $item->code){
                $confirm_phone = true;
            }
        }
        if(!$confirm_phone){
            $this->addError('sms_code', 'Неправильный код');
            return null;
        }

        $user->$param = ($param == 'sms_confirm') ? true : false;
        $user->save();

        return true;
    }

}
