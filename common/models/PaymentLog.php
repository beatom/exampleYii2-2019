<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property double $size
 * @property integer $payment_system
 * @property string $system_payment_id
 * @property boolean $completed
 * @property date $date_add
 * @property integet $payway_id
 * @property boolean $to_execute
 * @property date $to_execute_time
 */
class PaymentLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_log';
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

    public static function addLog($user_id, $summ, $payment_system, $payway_id = null)
    {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }
        $newLog = new static();
        $newLog->user_id = $user_id;
        $newLog->size = $summ;
        $newLog->payment_system = $payment_system;
        $newLog->payway_id = $payway_id;
        if($newLog->save()) {
            return $newLog;
        }
        return false;
    }

}
