<?php
namespace backend\models;

use common\models\BalanceBonusLog;
use common\service\Servis;
use yii\base\Model;
use common\models\User;
use common\models\Shares;

/**
 * Signup form
 */
class AddBonusLogForm extends Model
{
    public $id;
    public $user_id;
    public $summ;
    public $summ_now;
    public $date_add;
    public $date_end;
    public $work_days;
    public $description;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id','user_id','summ','summ_now', 'date_add', 'date_end','work_days','description'],'safe'],
        ];
    }


    public function add( $add = true, $shares=null )
    {
        if (!$this->validate()) {
            return null;
        }
        if(!$this->user_id){
            $this->addError('summ_now', 'обратитесь к разработчикам');
            return null;
        }

        if(!$this->id){
            $bonus_log = new BalanceBonusLog();
            $date_end = ($this->date_end)? $this->date_end.' 00:00:00': null;
            $work_days = ($this->work_days) ? $this->work_days: '0';
            return $bonus_log->add($this->summ_now, $this->user_id, $work_days, $this->description, $date_end);
        }

        $bonus_log = BalanceBonusLog::findIdentity($this->id);
        $bonus_log->summ = $this->summ;
        $bonus_log->summ_now = $this->summ_now;
        $bonus_log->work_days = ($this->work_days) ? $this->work_days: '0';
        $bonus_log->date_end = $this->date_end ? $this->date_end.' 00:00:00' : date('Y-m-d H:i:s', strtotime( $this->date_add . ' +' .  $bonus_log->work_days . ' days'));
        $bonus_log->description = $this->description;
        return $bonus_log->save();
    }

    public function setData($shares){
        $this->id = $shares->id;
        $this->user_id = $shares->user_id;
        $this->summ = $shares->summ;
        $this->summ_now = $shares->summ_now;
        $this->date_add = ($shares->date_add)? substr($shares->date_add,0,10) :'';
        $this->date_end = ($shares->date_end)? substr($shares->date_end,0,10) :'';
        $this->work_days = $shares->work_days;
        $this->description = $shares->description;
    }
}
