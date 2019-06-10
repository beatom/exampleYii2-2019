<?php
namespace common\models;

use common\service\ParserHtml;
use common\service\Servis;
use yii\db\ActiveRecord;
use common\models\BalanceLog;
use common\models\ChatTemplate;
use common\models\BalancePartnerLog;
use common\models\trade\Solution;
use common\models\trade\Investment;
use common\models\trade\TradingAccount;
use common\models\trade\TradingAccountChangeHistory;
use common\models\trade\InvestmentDailyLog;
use common\service\api_terminal\ApiTerminal;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $summ
 * @property date $date_open
 * @property date $date_close
 * @property integer $is_dolg
 * @property double $user_balance
 * @property double $percent
 * @property double $full_summ
 * @property string $start_comment
 * @property string $end_comment
 */
class Overdraft extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'overdraft';
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

    public static function findByUserId($user_id)
    {
        return static::findOne(['user_id' => $user_id]);
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

    public static function checkOpen($user_id, $summ, $date, $confirm = false)
    {

        $res = ['success' => false];
        $user = User::findIdentity($user_id);

        if ($summ < 1) {
            $res['message'] = 'Сумма должна быть больше 0';
            return $res;
        }
        if ($user->balance < $summ) {
            $res['message'] = 'Сумма овердрафта не может превышать текущую сумму';
            return $res;
        }
        $overdraft = Overdraft::find()->where(['user_id' => $user_id, 'is_dolg' => 1])->andWhere(['>', 'summ', 0])->one();
        $enable = true;

        if ($overdraft) {
            if ($overdraft->is_dolg || $overdraft->summ != 0) {
                $enable = false;
                $res['message'] = 'Нельзя активировать овердрафт не погасив первый';
                $res['success'] = false;
                return $res;
            }
        }

        if ($enable && $confirm) {
            
            $overdraft = new Overdraft();
            $overdraft->user_id = $user_id;
            $user_balance = $user->getFullBalanceWithComments();
            $overdraft->user_balance = $user_balance['balance'] + $summ;
            $overdraft->percent = $summ / ($overdraft->user_balance / 100);
            $overdraft->full_summ = $summ;
            $overdraft->summ = $summ;
            $overdraft->start_comment = $user_balance['message'];
            
            $overdraft->is_dolg = 1;
            $overdraft->date_open = date("Y-m-d");
            $date = \DateTime::createFromFormat('d-m-Y', $date);
            $overdraft->date_close = date('Y-m-d', $date->getTimestamp());
            $overdraft->save();

            $comment = 'Пополнение через Овердрафт';
            $balanse_log = new BalanceLog();
            $balanse_log->addLog($user_id, $summ, BalanceLog::deposit, BalanceLog::done, BalanceLog::overdraft, null, $comment);
            $balanse_log->save();

            $user->balance = $user->balance + $summ;
            $user->save();

            //send message
            ChatTemplate::sendMessageFromTemplate($user_id, ChatTemplate::overdraftOpen, ['amount' => $summ, 'date_close' => date('d-m-Y', strtotime($overdraft->date_close))]);


            $res['success'] = true;
        } else if (!$confirm) {
            $res['success'] = true;
            $res['popup'] = [
                'heder' => 'Оформить?',
                'content' => '<p>Оформить Овердрафт на сумму ' . $summ . '$ до ' . $date . '?</p>
 												<p><a href="/news/1-overdraft">Узнать подробнее</a></p>
 												<p><button class="u-link-action c-referral__btn" name="overconfirm"><span>Пополнить</span></button></p>'
            ];
            return $res;
        }
        return $res;
    }

    public static function closeDolg($user_id, $summ)
    {
        return $summ;
        $summ_user = $summ;
        $overdraft = Overdraft::find()->where(['user_id' => $user_id, 'is_dolg' => 1])->andWhere(['>', 'summ', 0])->one();
        if ($overdraft) {
                $summ_dolg = $overdraft->summ;
                if ($summ_user >= $summ_dolg) {
                    $overdraft->summ = 0;
                    $overdraft->is_dolg = 0;
                    $summ_user = $summ_user - $summ_dolg;

                    $comment = 'Закрытие овердрафта';
                    $tmp_sum = $summ_dolg;

                    //send message
                    ChatTemplate::sendMessageFromTemplate($user_id, ChatTemplate::overdraftClose, ['amount' => $summ_dolg, 'date_close' => date('d-m-Y', strtotime($overdraft->date_close))]);

                } else {
                    $tmp_sum = $summ_user;
                    $overdraft->summ = $summ_dolg - $summ_user;
                    $summ_user = 0;
                    $comment = 'Погашение части долга по овердрафту';
                }

                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user_id, -$tmp_sum, 6, BalanceLog::done, BalanceLog::overdraft, null, $comment);
                $balanse_log->save();

                $overdraft->save();
        }
        return $summ_user;
    }

    public static function is_dolg($user_id)
    {
        $overdraft = Overdraft::find()
	        ->where('user_id = '.$user_id)
	        ->andWhere('is_dolg = 1')
	        ->limit(1)
	        ->all();

        if (!empty($overdraft) ) {
            return true;
        }
        return false;
    }

    public static function weekLeft()
    {

        $date = date('Y-m-d', time() + 604800);
        $overdrafts = Overdraft::find()
            ->where('is_dolg = 1')
            ->andWhere('summ > 0')
            ->andWhere('date_close = "' . $date . '"')
            ->all();

        foreach ($overdrafts as $item) {
            //send message
            ChatTemplate::sendMessageFromTemplate($item->user_id, ChatTemplate::overdraftTimeWeek, ['amount' => $item->summ, 'date_close' => date('d-m-Y', strtotime($item->date_close))]);
        }
    }

    public static function takeOffDebts()
    {
        $date = date('Y-m-d 23:59:59',strtotime(' -1 day'));
        $overdrafts = static::find()
            ->where('is_dolg = 1')
            ->andWhere('summ > 0')
            ->andWhere('date_close < "' . $date . '"')
            ->all();

        foreach ($overdrafts as $overdraft) {
            $overdraft->rollover();
        }
    }
    
    
    public function rollover() {
        $date = date('Y-m-d H:i:s');
        $payed_summ = 0;
        $user = $this->user;
        $comment = $this->rolloverWithComments();
        $this->refresh();
        $this->end_comment = $comment;
        $user->refresh();
        $user_balance = $user->getFullBalanceWithComments();
        if($user_balance['balance'] > $this->user_balance) {
            $this->summ = $this->summ + ($user_balance['balance'] - $this->user_balance) * ($this->percent/100);
        }
        // Шаг 1 - списываем долг с баланса пользователя
        if ($user->balance > 0) {
            if ($user->balance >= $this->summ) {
                $this->is_dolg = 0;
                $user->balance -= $this->summ;
                $comment = 'Закрытие овердрафта';
                $tmp_sum = $this->summ;
                $this->summ = 0;
            } else {
                $tmp_sum = $user->balance;
                $this->summ -= $user->balance;
                $user->balance = 0;
                $comment = 'Погашение части долга по овердрафту';
            }
            $payed_summ += $tmp_sum;
            $balance_log = new BalanceLog();
            $balance_log->addLog($user->id, -$tmp_sum, 6, BalanceLog::done, BalanceLog::overdraft, null, $comment);
            $balance_log->save();
        }
        // Шаг 2 - списываем долг с баланса партнера
        if($user->balance_partner > 0 AND $this->is_dolg) {
            if ($user->balance_partner >= $this->summ) {
                $this->is_dolg = 0;
                $user->balance_partner -= $this->summ;
                $comment = 'Закрытие овердрафта';
                $tmp_sum = $this->summ;
                $this->summ = 0;
            } else {
                $tmp_sum = $user->balance_partner;
                $this->summ -= $user->balance_partner;
                $user->balance_partner = 0;
                $comment = 'Погашение части долга по овердрафту';
            }
            $payed_summ += $tmp_sum;
            $balance_partner_log = new BalancePartnerLog();
            $balance_partner_log->user_id = $user->id;
            $balance_partner_log->status = 0;
            $balance_partner_log->summ = -$tmp_sum;
            $balance_partner_log->description = $comment;
            $balance_partner_log->save();
        }
        // Шаг 3 - списываем долг с инвестиций в готовое решение
        if($this->is_dolg AND Investment::find()->where(['user_id' => $user->id, 'bonus_money' => null, 'deleted' => false])->andWhere('solution_id IS NOT NULL')->sum('summ_current') > 0) {
            foreach (Investment::find()->where(['user_id' => $user->id, 'bonus_money' => null, 'deleted' => false])->andWhere('solution_id IS NOT NULL')->all() as $solution_investment) {
                if($solution_investment->summ_current <= 0) {
                    continue;
                }
                if ($solution_investment->summ_current  >= $this->summ) {
                    $this->is_dolg = 0;
                    $solution_investment->summ_current -= $this->summ;
                    $comment = 'Закрытие овердрафта';
                    $tmp_sum = $this->summ;
                    $this->summ = 0;
                } else {
                    $tmp_sum = $solution_investment->summ_current ;
                    $this->summ -= $solution_investment->summ_current ;
                    $solution_investment->summ_current  = 0;
                    $comment = 'Погашение части долга по овердрафту';
                }
                $payed_summ += $tmp_sum;
                $solution_investment->makeLog(2, $tmp_sum);
                if($solution_investment->summ_current <= 0) {
                    InvestmentDailyLog::deleteByInvestmentId( $solution_investment->id);
                    $solution_investment->delete();
                } else {
                    $day_log = $solution_investment->getLastPeriodLog();
                    $day_log->summ_withdraw -= $tmp_sum;
                    $day_log->save();
                    $solution_investment->save();
                }
                $balanceLog = new BalanceLog();
                $balanceLog->addLog($user->id, $tmp_sum, 5, 1, 0, null, 'Возврат инвестиции в готовое решение');
                $balanceLog->save();
                $newbalanceLog = new BalanceLog();
                $newbalanceLog->addLog($user->id, -$tmp_sum, 6, BalanceLog::done, BalanceLog::overdraft, null, $comment);
                $newbalanceLog->save();

                if($this->is_dolg == 0 ) {
                    break 1;
                }
            }
        }

        // Шаг 4 - списываем долг с торговых счетов пользователя(НЕ ДУ)
        if($this->is_dolg) {
            foreach (TradingAccount::find()->where(['user_id' => $user->id, 'is_du' => 0])->andWhere(['<>', 'type_account', 4])->all() as $account) {
                $trader_investment = $account->getTraderInvestment();

                $terminal = ApiTerminal::getInstance();
                if($response = $terminal->getTradeAccountInfo($account->account_number)) {
                    if($response['balance'] > $response['freeBalance'] AND $response['freeBalance'] < $this->summ) {
                        TradingAccountChangeHistory::createNewAccount($account->id);
                        $account->refresh();
                    }
                }

                if ($trader_investment->summ_current  >= $this->summ) {
                    $this->is_dolg = 0;
                    $trader_investment->summ_current -= $this->summ;
                    $comment = 'Закрытие овердрафта';
                    $tmp_sum = $this->summ;
                    $this->summ = 0;
                } else {
                    $tmp_sum = $trader_investment->summ_current ;
                    $this->summ -= $trader_investment->summ_current ;
                    $trader_investment->summ_current  = 0;
                    $comment = 'Погашение части долга по овердрафту';
                }
                $payed_summ += $tmp_sum;
                if(!$account->create_admin) {
                    $terminal->insertDeposit($account->utip_account_id, $tmp_sum, 'take_out');
                }
                $trader_investment->makeLog(2, $tmp_sum);

                $account_day_log = $account->getLastPeriodLog();
                $account_day_log->summ_add -= $tmp_sum;
                $account_day_log->save();

                $day_log = $trader_investment->getLastPeriodLog();
                $day_log->summ_withdraw -= $tmp_sum;
                $day_log->save();

                $balanceLog = new BalanceLog();
                $balanceLog->addLog($user->id, $tmp_sum, 5, 1, 0, null, 'Снятие средств с собственного торгового счета');
                $balanceLog->save();
                $newbalanceLog = new BalanceLog();
                $newbalanceLog->addLog($user->id, -$tmp_sum, 6, BalanceLog::done, BalanceLog::overdraft, null, $comment);
                $newbalanceLog->save();
                $trader_investment->save();
                if($this->is_dolg == 0 ) {
                    break 1;
                }
            }
        }

        // Шаг 5 - списываем долг с инвестиций в счета ДУ (в том числе собственные)
        if($this->is_dolg) {
            $investments = Investment::find()->where(['user_id' => $user->id, 'bonus_money' => null, 'deleted' => false])->andWhere('trading_account_id IS NOT NULL')->all();

            foreach ($investments as $investment) {

                if(!$investment->account->is_du OR $investment->summ_current <= 0) {
                    continue;
                }

                $account = $investment->account;

                if ($investment->summ_current  >= $this->summ) {
                    $this->is_dolg = 0;
                    $investment->summ_current -= $this->summ;
                    $comment = 'Закрытие овердрафта';
                    $tmp_sum = $this->summ;
                    $this->summ = 0;
                } else {
                    $tmp_sum = $investment->summ_current ;
                    $this->summ -= $investment->summ_current ;
                    $investment->summ_current  = 0;
                    $comment = 'Погашение части долга по овердрафту';
                }
                $payed_summ += $tmp_sum;
                $investment->makeLog(2, $tmp_sum);

                if($account->inTradingPeriod()) {
                    $admInvestment = $account->getAdminInvestment();

                    if(date('Y-m-d', strtotime($date)) == date('Y-m-d', strtotime($admInvestment->date_add))) {
                        $admInvestment->date_add = date('Y-m-d H:i:s', strtotime($admInvestment->date_add . ' -1 day'));
                    }

                    $admInv_log = $admInvestment->getLastPeriodLog();
                    $admInv_log->summ_withdraw += $tmp_sum;
                    $admInvestment->makeLog(3, $tmp_sum);
                    $admInv_log->save();
                } else {
                    if(!$account->create_admin) {
                        $terminal = ApiTerminal::getInstance();
                        if($response = $terminal->getTradeAccountInfo($account->account_number)) {
                            if($response['balance'] > $response['freeBalance'] AND $response['freeBalance'] < $this->summ) {
                              //  TradingAccountChangeHistory::createNewAccount($account->id);
                              //  $account->refresh();
                            }
                            if(!$account->create_admin) {
                                $terminal->insertDeposit($account->utip_account_id, $tmp_sum, 'take_out');
                            }
                        }
                    }
                }

                $account_day_log = $account->getLastPeriodLog();
                $account_day_log->summ_withdraw -= $tmp_sum;
                $account_day_log->save();

                $day_log = $investment->getLastPeriodLog();
                $day_log->summ_withdraw -= $tmp_sum;
                $day_log->save();

                $balanceLog = new BalanceLog();
                $balanceLog->addLog($user->id, $tmp_sum, 5, 1, 0, null, 'Возврат средств с инвестиции в счет ДУ');
                $balanceLog->save();
                $newbalanceLog = new BalanceLog();
                $newbalanceLog->addLog($user->id, -$tmp_sum, 6, BalanceLog::done, BalanceLog::overdraft, null, $comment);
                $newbalanceLog->save();
                $investment->save();

                if($this->is_dolg == 0 ) {
                    break 1;
                }
            }
        }


        ChatTemplate::sendMessageFromTemplate($user->id, ChatTemplate::overdraftClose, ['amount' => Servis::getInstance()->beautyDecimal($payed_summ, 2), 'date_close' => $date]);
        $this->is_dolg = 0;
        $this->save();
        $user->save();
        
    }



    public function rolloverWithComments() {
        if($this->summ <= 0) {
            return false;
        }
        $date = date('Y-m-d H:i:s');
        $message = '';
        $payed_summ = 0;
        $user = $this->user;

        $user_balance = $user->getFullBalanceWithComments();
        $message .= $user_balance['message'].'<hr>';
        $message .= "Долг по овердрафту = $this->summ </br>";
        if($user_balance['balance'] > $this->user_balance) {
            $oldSum = $this->summ;
            $this->summ = $this->summ + ($user_balance['balance'] - $this->user_balance) * ($this->percent/100);
            $message .= "Пользователь получил прибыль, долг увеличивается до: </br>";
            $message .= "$oldSum  + (".$user_balance['balance']." - $this->user_balance) * ($this->percent / 100)  = $this->summ </br>";
        } else {
            $message .= "Пользователь понес убыток или баланс остался прежним, долг не увеличивается </br>";
        }
        $message .= "<hr>";
        // Шаг 1 - списываем долг с баланса пользователя
        if ($user->balance > 0) {
            if ($user->balance >= $this->summ) {
                $this->is_dolg = 0;
                $payed_summ += $this->summ;
                $user->balance -= $this->summ;
                $tmp_sum = $this->summ;
                $this->summ = 0;
            } else {
                $tmp_sum = $user->balance;
                $this->summ -= $user->balance;
                $user->balance = 0;
            }
            $message .= "$tmp_sum$ списывается с основного баланса </br>";
        }
        // Шаг 2 - списываем долг с баланса партнера
        if($user->balance_partner > 0 AND $this->is_dolg) {
            if ($user->balance_partner >= $this->summ) {
                $this->is_dolg = 0;
                $payed_summ += $this->summ;
                $user->balance_partner -= $this->summ;
                $tmp_sum = $this->summ;
                $this->summ = 0;
            } else {
                $tmp_sum = $user->balance_partner;
                $this->summ -= $user->balance_partner;
                $user->balance_partner = 0;
            }
            $message .= "$tmp_sum$ списывается с партнерского счета </br>";
        }
        // Шаг 3 - списываем долг с инвестиций в готовое решение
        if($this->is_dolg AND Investment::find()->where(['user_id' => $user->id, 'bonus_money' => null, 'deleted' => false])->andWhere('solution_id IS NOT NULL')->sum('summ_current') > 0) {
            foreach (Investment::find()->where(['user_id' => $user->id, 'bonus_money' => null, 'deleted' => false])->andWhere('solution_id IS NOT NULL')->all() as $solution_investment) {
                if($solution_investment->summ_current <= 0) {
                    continue;
                }
                if ($solution_investment->summ_current  >= $this->summ) {
                    $this->is_dolg = 0;
                    $payed_summ += $this->summ;
                    $solution_investment->summ_current -= $this->summ;
                    $tmp_sum = $this->summ;
                    $this->summ = 0;
                } else {
                    $tmp_sum = $solution_investment->summ_current ;
                    $this->summ -= $solution_investment->summ_current ;
                    $solution_investment->summ_current  = 0;
                }
                $message .= "$tmp_sum$ списывается с инвестиции в готовое решение </br>";

                if($this->is_dolg == 0 ) {
                    break 1;
                }
            }
        }

        // Шаг 4 - списываем долг с торговых счетов пользователя(НЕ ДУ)
        if($this->is_dolg) {
            foreach (TradingAccount::find()->where(['user_id' => $user->id, 'is_du' => 0])->andWhere(['<>', 'type_account', 4])->all() as $account) {
                $trader_investment = $account->getTraderInvestment();
                if ($trader_investment->summ_current  >= $this->summ) {
                    $this->is_dolg = 0;
                    $payed_summ += $this->summ;
                    $trader_investment->summ_current -= $this->summ;
                    $tmp_sum = $this->summ;
                    $this->summ = 0;
                } else {
                    $tmp_sum = $trader_investment->summ_current ;
                    $this->summ -= $trader_investment->summ_current ;
                    $trader_investment->summ_current  = 0;
                }
                $message .= "$tmp_sum$ списывается с торгового счета пользователя $account->name </br>";
                if($this->is_dolg == 0 ) {
                    break 1;
                }
            }
        }

        // Шаг 5 - списываем долг с инвестиций в счета ДУ (в том числе собственные)
        if($this->is_dolg) {
            $investments = Investment::find()->where(['user_id' => $user->id, 'bonus_money' => null, 'deleted' => false])->andWhere('trading_account_id IS NOT NULL')->all();
            foreach ($investments as $investment) {
                if(!$investment->account->is_du OR $investment->summ_current <= 0) {
                    continue;
                }
                $account = $investment->account;
                if ($investment->summ_current  >= $this->summ) {
                    $this->is_dolg = 0;
                    $payed_summ += $this->summ;
                    $investment->summ_current -= $this->summ;
                    $tmp_sum = $this->summ;
                    $this->summ = 0;
                } else {
                    $tmp_sum = $investment->summ_current ;
                    $this->summ -= $investment->summ_current ;
                    $investment->summ_current  = 0;
                }
                if($account->user_id == $this->user_id) {
                    $message .= "$tmp_sum$ списывается с инвестиции в собственный счет доверительного управления $account->name </br>";
                } else {
                    $message .= "$tmp_sum$ списывается с инвестиции в счет доверительного управления $account->name </br>";
                }
                
                if($this->is_dolg == 0 ) {
                    break 1;
                }
            }
        }
        if($this->is_dolg) {
            $message .= "сумма долга $this->summ сгорает </br>";
        }
        return $message;
    }

}
