<?php
namespace common\models\logs;

use yii\db\ActiveRecord;
use common\models\User;
use common\models\trade\TradingAccount;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $trading_account_id
 * @property integer $status
 */
class LogDemoBonus extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'log_demo_bonus';
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

    public function getAccount()
    {
        return $this->hasOne(TradingAccount::class, ['id' => 'trading_account_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function logExist( $user_id, $account_id ){
        $exists = static::find()->where(['user_id' => $user_id, 'trading_account_id' => $account_id, 'status' => [1, 2]])->exists();
        return $exists ? true : false;
    }

    public static function getCountNewLogs(){
        return static::find()->where(['status' => 1])->count();
    }


}
