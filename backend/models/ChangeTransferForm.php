<?php
namespace backend\models;

use common\models\BalanceLog;
use yii\base\Model;
use common\models\User;
use common\models\EmailTemplate;
use common\models\QueueMail;
use Yii;
use common\models\ChatTemplate;

/**
 * Signup form
 */
class ChangeTransferForm extends Model
{
    //user
    public $id;
    public $date_add;
    public $user_id;
    public $summ;
    public $system;
    public $operation;
    public $status;
    public $comment;
    public $recipient_user_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id','summ', 'status','operation'], 'required'],
            [['system', 'date_add', 'comment','recipient_user_id'],'safe'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function change( $balance_log, $not_recipe = false )
    {
        if (!$this->validate()) {
            return null;
        }

        //изменяем статус с впроцесе
        if( $balance_log->status != $this->status && in_array($balance_log->status, [ BalanceLog::in_processing, 3])
            && ($balance_log->operation == BalanceLog::transfer || $balance_log->operation == BalanceLog::exit_deposit) ){

            $sender = User::findIdentity($this->user_id);
            $summ = $this->summ * (-1);

            //отменен
            if($this->status == BalanceLog::canceled){
                $balance_log->status = 4;
                $balance_log->comment = $this->comment;
                $balance_log->execution_time = null;
//                $sender->balance += abs($this->summ);
//                $sender->save();
                //send message
                $invoice = str_replace('withdraw_', null, $balance_log->hash_payment);
                ChatTemplate::sendMessageFromTemplate($sender->id,ChatTemplate::templateWithdrawDenial,
                    [
                        //{{amount}} {{currency}} через сервис {{service}} на счет {{invoice}}
                        'amount' => $summ ,
                        'service' => BalanceLog::$system[$balance_log->system] ,
                        'currency'=>'',
                        'invoice'=> $invoice,
                    ]);

                return $balance_log->save();
            }
            //выполнен
            else if($this->status == BalanceLog::done ){
                $balance_log->status = $this->status;
                $balance_log->comment = $this->comment;


                $res = $balance_log->save();
                if($res){

                    //если внутрений перевод
                    if(!$not_recipe) {
                        $recipient_user = User::findIdentity($balance_log->recipient_user_id);
                        $recipient_user->balance += $summ;
                        $recipient_user->save();

                        $balanse_log_resipies = new BalanceLog();
                        $balanse_log_resipies->addLog($recipient_user->id, $summ, BalanceLog::transfer, BalanceLog::done, BalanceLog::internal_transfer, null, 'получение личных средств от user_id = ' . $this->user_id . ' Ник = ' . $sender->username, $sender->id);
                        $balanse_log_resipies->save();

                        //send message
                        $invoice = str_replace('withdraw_', null, $balance_log->hash_payment);
                        ChatTemplate::sendMessageFromTemplate($sender->id,ChatTemplate::templateWithdrawApprove,
                            [
                                //{{amount}} {{currency}} через сервис {{service}} на счет {{invoice}}
                                'amount' => $summ ,
                                'service' => BalanceLog::$system[$balance_log->system] ,
                                'currency'=>'$',
                                'invoice'=> $invoice,
                            ]);

                        //отправка на почту о получении средств
                        $data = [];
                        $data['username'] = $sender->username;
                        $data['summ'] = $summ;
                        $data['resipientusername'] = $recipient_user->username;

                        QueueMail::addTask(
                            Yii::$app->params['supportEmail'],
                            $recipient_user->email,
                            '',
                            EmailTemplate::receiving_transfer,
                            $data );

                        $data = [];
                        $data['username'] = $sender->username;
                        $data['summ'] = $summ;
                        $data['resipientusername'] = $recipient_user->username;
                        $data['id_log'] = $balanse_log_resipies->id;
                        QueueMail::addTask(
                            Yii::$app->params['supportEmail'],
                            $sender->email,
                            '',
                            EmailTemplate::approval_transfer,
                            $data );
                    }
                    else{

                        //send message
                        $invoice = str_replace('withdraw_', null, $balance_log->hash_payment);
                        ChatTemplate::sendMessageFromTemplate($sender->id,ChatTemplate::templateWithdrawApprove,
                            [
                                'amount' => $summ ,
                                'service' => BalanceLog::$system[$balance_log->system] ,
                                'currency'=>'$',
                                'invoice'=> $invoice,
                            ]);

                    }

                    return true;
                }
            } else {
                $balance_log->status = $this->status;
                $balance_log->save();
            }
        }

        return true;
    }

    public function setData($balance_log){

        $this->id = $balance_log->id;
        $this->date_add = $balance_log->date_add;
        $this->summ = $balance_log->summ;
        $this->user_id = $balance_log->user_id;
        $this->system = $balance_log->system;
        $this->operation = $balance_log->operation;
        $this->status = $balance_log->status;
        $this->comment = $balance_log->comment;
        $this->recipient_user_id = $balance_log->recipient_user_id;
    }
}
