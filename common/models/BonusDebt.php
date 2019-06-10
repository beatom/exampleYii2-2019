<?php
namespace common\models;

use common\service\Servis;
use phpDocumentor\Reflection\Types\Null_;
use yii\db\ActiveRecord;
use common\models\EmailTemplate;
use Yii;
use common\models\trade\Investment;
use common\models\User;
use common\models\ChatTemplate;
use common\models\Overdraft;
/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $summ
 * @property integer $status
 * @property date $date_add

 */
class BonusDebt extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bonus_debt';
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


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function getUserDebts($user_id)
    {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }
        return static::find()->where(['user_id' => $user_id])->orderBy('status')->all();
    }


    public static function sumUserDebts($user_id)
    {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }
        return static::find()->where(['user_id' => $user_id, 'status' => 1])->sum('summ');
    }


    public static function add($user_id, $summ)
    {
        if(!$user_id OR $summ <=0.01 ) {
            return false;
        }
        
        $user = User::findIdentity($user_id);

        $summ = number_format($summ, 2);
        $newRecord = new static();
        $newRecord->user_id = $user_id;
        $newRecord->summ = $summ;
        $newRecord->save();

        $allDebtAmount = static::sumUserDebts($user_id);
        $message = static::needed($user_id);

        ChatTemplate::sendMessageFromTemplate($user_id, ChatTemplate::bonusDebtInsert, ['sum' => number_format($summ, 2), 'amount' => $allDebtAmount, 'need' => $message ]);
      //  SmsManager::sendOne(SmsTemplate::templateEndedBonusesIncome, $user->phone, ['sum' => number_format($summ, 2), 'amount' => $allDebtAmount, 'need' => $message ], $user_id);
            
        return true;
    }

    public static function payOutUserDebts($user_id)
    {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }

        if($user->verified AND $user->hasMinDeposit()) {

            Investment::updateAll(['show_till_verification' => false],['user_id' => $user->id, 'deleted' => true, 'show_till_verification' => true]);

            $debtSumm = 0;
            foreach (static::find()->where(['user_id' => $user_id, 'status' => 1])->all() as $debt) {
                $debtSumm += $debt->summ;
                $debt->status = 2;
                $debt->save();
            }

            if($debtSumm > 0) {
                ChatTemplate::sendMessageFromTemplate($user_id, ChatTemplate::bonusDebtClose, ['amount' => $debtSumm]);
                $balanceLog = new BalanceLog();
                $balanceLog->addLog($user_id, $debtSumm, 5, 1, 0, null, 'Прибыль от инвестирования бонусов');
                $balanceLog->save();
                $debtSumm = Overdraft::closeDolg($user->id, $debtSumm);
                if($debtSumm > 0) {
                    $user->balance += $debtSumm;
                }
            }
            $user->save();
            return true;
        }
        return false;
    }


    public static function needed($user_id, $chat = true)
    {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }
        $message = '';
        if($chat) {
            if(!$user->verified AND !$user->hasMinDeposit()) {
                $message = 'пройти верификацию с помощью загрузки документов в настройках и пополнить счет минимум на 10$';
            } elseif(!$user->verified) {
                $message = 'пройти верификацию с помощью загрузки документов';
            } elseif (!$user->hasMinDeposit()) {
                $message = 'пополнить счет минимум на 10$';
            }
        } else {
            if(!$user->verified AND !$user->hasMinDeposit()) {
                $message = 'совершить минимальное пополнение и верифицировать аккаунт.';
            } elseif (!$user->hasMinDeposit()) {
                $message = 'совершить минимальное пополнение.';
            } elseif(!$user->verified) {
                $message = 'верифицировать аккаунт.';
            }
        }

        return $message;
    }


}
