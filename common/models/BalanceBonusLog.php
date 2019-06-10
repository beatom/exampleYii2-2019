<?php
namespace common\models;

use common\service\Servis;
use phpDocumentor\Reflection\Types\Null_;
use yii\db\ActiveRecord;
use common\models\EmailTemplate;
use Yii;
use common\models\trade\Investment;
/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $summ
 * @property integer $summ_now
 * @property date $date_add
 * @property date $date_end
 * @property integer $work_days
 * @property string $description
 */
class BalanceBonusLog extends ActiveRecord
{

    const work_days = 11;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_bonus_log';
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

    public function getInvestments()
    {
        return $this->hasMany(Investment::class, ['bonus_money' => 'id'])->where(['deleted' => false]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function add($summ, $user_id, $work_days, $description = '', $date_end=null){
        $this->summ = $summ;
        $this->summ_now = $summ;
        $this->user_id = $user_id;
        $this->work_days = $work_days;
        $this->description = $description;

        if($date_end){
            $this->date_end = $date_end;
        }

        $res = $this->save();

        if($res){
            $res = User::BonuceBalanceUp($user_id, $summ, true);
        }

        $user = User::findIdentity($user_id);
        if(!$user) {
	        return false;
        }
        $data = [];
        $data['user_name'] = $user->username;
        $data['summ'] = $summ;
        $data['description'] = $description;

        if($summ > 0) {

            QueueMail::addTask(
                Yii::$app->params['supportEmail'],
                $user->email,
                '',
                EmailTemplate::BALANCE_BONUS_UP,
                $data );

//            $email_template = EmailTemplate::findIdentity(EmailTemplate::BALANCE_BONUS_UP);
//            $res = $email_template->getEmailTemplate($data);
//            $email_template->sendMail($user->email, $res['title'], $res);
        }

        $message = '<p>Пользователь '.$user->username.' получил бонус на сумму '. $summ.'</p><p>'.$description.'</p>';

        QueueMail::addTask(Yii::$app->params['supportEmail'],
            Yii::$app->params['adminEmail'],
            'Зачисление бонуса пользователю',
            'adminMessage',
            $message );


        return $res;
    }

    public static function getEndedBonuses()
    {
        $now = date("Y-m-d H:i:s");
        $request = static::find()->where(['<', 'date_end', $now])->andWhere(['expired' => 0]);
        return $request->all();
    }

    public static function getUserActiveBonuses($user_id)
    {
        $select = array('id', 'summ_now', 'work_days', 'date_end');
        $bonuses = static::find()->where(['user_id' => $user_id, 'expired' => 0])->andWhere(['>','summ_now', 0])->orderBy('date_end DESC')->select($select)->asArray()->all();
        $service = Servis::getInstance();
        foreach ($bonuses as $key => $value) {
            if($value['date_end'] != null) {
                $bonuses[$key]['work_days'] = $service->daysToDate($value['date_end']);
            }
            $bonuses[$key]['days_word'] =  $service->wordOfDays($bonuses[$key]['work_days']);
        }
        usort($bonuses, function($a,$b){return $a['work_days']-$b['work_days'];});
        return $bonuses;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


}
