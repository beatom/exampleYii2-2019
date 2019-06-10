<?php
namespace common\models;

use common\service\api\AmoCrm;
use yii\db\ActiveRecord;
use common\models\User;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property date $date_add
 * @property string $session_id
 */
class UserIpLogAdmin extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ip_log_admin';
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

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    public static function setLog($user_id, $ip, $session_id){
        if(!$log = static::find()->where(['user_id' => $user_id, 'ip' => $ip, 'session_id' => $session_id])->andWhere('date_add > "'.date('Y-m-d H:i:s', strtotime('-10 minutes')).'"')->orderBy('id DESC')->one()) {
            $log = new static();
            $log->user_id = $user_id;
            $log->ip = $ip;
            $log->session_id = $session_id;
        }
        $log->date_add = date("Y-m-d H:i:s", time());
        $log->save();
    }

    public static function getLastLogs(){
        $ips = explode(",", Options::getOptionValueByKey('login_admin_white_ip'));
        $logs = static::find()->andWhere('date_add > "'.date('Y-m-d H:i:s', strtotime('-5 minutes')).'"')->orderBy('id DESC')->all();

        $admin = [];
        $non_admin = [];
        foreach ($logs as $log) {
            if(in_array($log->ip, $ips)) {
                $admin[] = $log;
            } else {
                $non_admin[] = $log;
            }
        }
        $logs = [
            'admin' => $admin,
            'non_admin' => $non_admin,
        ];
        return $logs;
    }


}
