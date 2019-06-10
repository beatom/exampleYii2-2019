<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;
use common\models\EmailTemplate;
use common\models\QueueMail;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $summ
 * @property integer $status
 * @property date $date_add
 * @property string $description
 * @property integer $from_user_id
 */
class BalancePartnerLog extends ActiveRecord
{

    const status_default = 0;
    const status_come_in = 1;   // основной доход
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_partner_log';
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
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPartner()
    {
        return $this->hasOne(User::class, ['id' => 'from_user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function add($summ, $user_id, $description = '', $status=0, $from_user_id=null, $comment = null){
        $this->summ = $summ;
        $this->user_id = $user_id;
        $this->description = $description;
        $this->status = $status;
        $this->from_user_id = $from_user_id;
        $this->comment = $comment;

        $res = $this->save();

        if($res){
            $res = User::PartnerBalanceUp($user_id, $summ, true);
        }
        return $res;

//        $user = User::findIdentity($user_id);
//        $data = [];
//        $data['user_name'] = $user->username;
//        $data['summ'] = $summ;
//        $data['description'] = $description;

//        if($summ > 0 AND !$from_user_id) {
//            QueueMail::addTask(
//                Yii::$app->params['supportEmail'],
//                $user->email,
//                '',
//                EmailTemplate::BALANCE_BONUS_UP,
//                $data );
//        }
//
//        $message = '<p>Пользователь '.$user->username.' получил '. $summ.'$ на партнерский счет</p><p>'.$description.'</p>';
//
//        QueueMail::addTask(Yii::$app->params['supportEmail'],
//            Yii::$app->params['adminEmail'],
//            'Зачисление бонуса пользователю',
//            'adminMessage',
//            $message );

        return $res;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getComeIn( $to_user_id, $from_user_id = null ){
        if($from_user_id) {
            $sql = 'SELECT SUM(`summ`) id from `balance_partner_log` WHERE `summ` > 0 AND `user_id` = ' . $to_user_id . ' AND `from_user_id` = ' . $from_user_id;
        }
        else{
            $sql = 'SELECT SUM(`summ`) id from `balance_partner_log` WHERE `summ` > 0 AND `user_id` = ' . $to_user_id;
        }
        $res = Yii::$app->db->createCommand($sql)->queryScalar();
        return ($res)? $res:0;
    }


    public static function getUserPartnerHonorarThisPeriod($user_id, $partner_id)
    {
        $this_saturday = date('Y-m-d', strtotime('this Saturday'));
        $partner_honorar = static::find()->where(['from_user_id' => $user_id, 'user_id' => $partner_id])->andWhere("DATE(date_add) = $this_saturday")->sum('summ');
        return $partner_honorar ? abs($partner_honorar) : 0;
    }


}
