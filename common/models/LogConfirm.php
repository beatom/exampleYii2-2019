<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property string $phone
 * @property string $email
 * @property string $code
 * @property integer $user_id
 *
 */
class LogConfirm extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_confirm';
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
        ];
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    public static function addSmsLog( $phone, $user_id = null ){
        return true;
        $code = rand( 10000, 99999 );
        $confirm = new LogConfirm();
        $confirm->date_add = time();
        $confirm->phone = $phone;
        $confirm->code = $code;
        if($user_id){
            $confirm->user_id = $user_id;
        }
        $confirm->save();
        return SmsManager::sendOne(SmsTemplate::templateWithdrawFounds, $phone, ['code' => $code], $user_id);
    }

    public static function getLogPhone($phone){
        return LogConfirm::find()->orderBy(' date_add DESC')->where('phone = '.$phone.' AND date_add > '. (time() - 3600 ))->limit(1)->all();
    }

}
