<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;
/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $status
 * @property date $date_add
 * @property string $vk
 * @property string $instagram
 */
class UserBonusRequest extends ActiveRecord
{

    public static $statuses = [
        1 => 'Новая заявка',
        2 => 'Заявка выполнена',
        3 => 'Заявка отклонена'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bonus_request';
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
            [['vk', 'instagram'], 'trim'],
            [['vk', 'instagram', 'user_id', 'status'], 'required', 'message' => Yii::t('app', 'Необходимо заполнить')],
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

    public static function countNewRequests()
    {
        return static::find()->where('status = 1')->count();
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function approveRequest() {
        if($this->status != 1 OR !$user = $this->user) {
            return false;
        }
        BalanceLog::add($this->user_id, $user->balance * 0.07, BalanceLog::bonus, BalanceLog::in_process, 0, null, 'Бонус +7%');
        $this->status = 2;
        $this->save();
        $user->seven_bonus_received = true;
        $user->save();
        ChatTemplate::sendMessageFromTemplate($this->user_id, ChatTemplate::bonus7success);
        return true;
    }

    public function declineRequest() {
        if($this->status != 1) {
            return false;
        }
        $this->status = 3;
        $this->save();
        return true;
    }
    
    public static function checkOpen($user_id) {
        $request = static::find()->where(['user_id'=>$user_id, 'status' =>1])->one();
        return $request ? $request : false;
    }
}
