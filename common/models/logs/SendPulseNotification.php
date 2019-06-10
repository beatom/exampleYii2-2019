<?php
namespace common\models\logs;

use yii\db\ActiveRecord;
use common\models\User;
use common\service\api\SendPulse;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $notification
 * @property string $data
 * @property date $date_add
 */
class SendPulseNotification extends ActiveRecord
{
    const registration = 1;
    const three_days = 2;
    const fifteen_days = 3;

    const addresses = [
        1 => '/events/name/registration',
        2 => '/events/name/ne_oplatili_72_chasa_3dnya',
        3 => '/events/name/ne_oplatili_360_chasa_15dnej',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sendpulse_notifications';
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

    public static function send($user_id, $notification_id, $data) {
        if(!key_exists($notification_id, static::addresses)) {
            return;
        }
        $new_log = new static();
        $new_log->user_id = $user_id;
        $new_log->notification = $notification_id;
        $new_log->data = json_encode($data);
        $new_log->save();

        SendPulse::getInstance()->curl(static::addresses[$notification_id], $data);
    }
}
