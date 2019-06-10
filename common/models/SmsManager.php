<?php
namespace common\models;

use common\models\SmsTemplate;
use yii\db\ActiveRecord;
use common\models\sms\prostor_JsonGate;
use common\models\sms\iqsms_JsonGate;
use common\models\SmsLog;
use common\service\LogMy;
use common\models\User;
use common\models\SmsBlock;

class SmsManager extends ActiveRecord
{
    const sender = 'MediaLine';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_managers';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_login', 'api_password'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function sendOne($messageTemplate, $phone, $code, $user_id = 0)
    {
        $smsProvider = static::getActiveSmsProvider();

        if (!$smsProvider) {
            return false;
        }
        
        $text = SmsTemplate::setMessage($messageTemplate, $code);
        $gate = static::getActiveGate($smsProvider);
        $phone = User::clearPhone( $phone );
        $messages = array(
            array(
                "clientId" => $user_id,
                "phone" => $phone,
                "text" => $text,
              //  "sender" => static::sender,
                'status' => null
            ),
        );

        $answer = $gate->send($messages);
        //todo поставить заглушку на смс если нужно


	   // LogMy::getInstance()->setLog(['answer ='=>$answer, '$phone ='=>$phone, '$text ='=>$text], 'smsmenedjers');

        if (isset($answer['messages'][0]['status']) &&  $answer['messages'][0]['status'] == 'accepted') {
            SmsLog::makeRecord($user_id, $phone, $text, $answer['messages'][0]['status'], $smsProvider->id, $answer['messages'][0]['smscId']);
            return true;

        }
        return false;
    }

    public static function getActiveGate($smsProvider)
    {
        switch ($smsProvider->class_name) {
            case "iqsms":
                $gate = new iqsms_JsonGate($smsProvider->api_login, $smsProvider->api_password);
                break;
            case "prostor":
                $gate = new prostor_JsonGate($smsProvider->api_login, $smsProvider->api_password);
                break;
            default:
                return false;
        }
        return $gate;
    }


    public static function getActiveSmsProvider()
    {
        $smsProvider = static::findOne(['is_active' => '1']);

        if (!$smsProvider) {
            return false;
        }

        return $smsProvider;
    }
    
    public static function stopSpam($number, $comment = null) {
        $number = User::clearPhone($number);
        if($message = SmsBlock::isBlocked($number)) {
            return $message;
        }
        $count = SmsLog::find()->where(['phone' => $number])->andWhere('date_add > "'.date('Y-m-d H:i:s', strtotime(' -20 minutes')). '"')->count();
        if($count > 5) {
            return SmsBlock::addBlock($number, $comment);
        }
        return false;
    }
    
}
