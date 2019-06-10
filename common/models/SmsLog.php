<?php
namespace common\models;

use yii\db\ActiveRecord;

class SmsLog extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_log';
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
        return [];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getManager()
    {
        return $this->hasOne(SmsManager::className(), ['id' => 'sms_manager_id']);
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function makeRecord($user_id,$phone, $text, $status, $sms_manager_id, $smscId)
    {
        $sms = new SmsLog;
        $sms->user_id = $user_id;
        $sms->phone = $phone;
        $sms->text = $text;
        $sms->date_add = date("Y-m-d H:i:s", time());
        $sms->status = $status;
        $sms->sms_manager_id = $sms_manager_id;
        $sms->smscId = $smscId;

        if($sms->save()) {
            return true;
        }
        return false;
    }


}
