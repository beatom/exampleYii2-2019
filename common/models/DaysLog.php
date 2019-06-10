<?php
namespace common\models;

if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}

use common\service\Servis;
use yii\db\ActiveRecord;
use Yii;
use common\models\BalanceLog;
use common\models\PartnerBasicIncome;
use common\service\PartnerProgram;
use common\models\User;
use common\models\Events;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property double $profit
 * @property double $sum_start
 * @property double $sum_end
 * @property string $comment
 * @property double $profit_sum
 */
class DaysLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'days_log';
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

    public function getEvents()
    {
        return $this->hasMany(Events::class, ['days_log_id' => 'id'])->
           orderBy(['date_add' => SORT_DESC]);
    }

    public function getEvents_complete()
    {
//        $from = $this->date_add . ' 15:00:00';
//        $to = date('Y-m-d 10:00:00', strtotime($this->date_add . ' +1 day'));
        return $this->hasMany(Events::class, ['days_log_id' => 'id'])->where("`show` = true")->
        orderBy(['date_add' => SORT_ASC]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }


    public static function getLog($date = false, $with = false)
    {
        if(!$date) {
            $date = date("Y-m-d");
        }
        $query = static::find()->where("date_add = '$date'");
        if($with AND method_exists(new static(), 'get' . $with)) {
            $query->with($with);
        }
        $return = $query->one();
        $return = $return ? $return : static::addLog($date);
        return $return;
    }

    public static function getPeriod()
    {
        if(date('H') >= 15) {
            $date =  date('Y-m-d');
        } else {
            $date =  date('Y-m-d', strtotime(' yesterday'));
        }
        if($log = static::getLog($date) AND $log->comment) {

        } else {
            $log = static::getLog($date);
        }
        return $log;
    }
    
    public static function addLog($date)
    {
        $newLog = new static();
        $newLog->date_add = date("Y-m-d", strtotime($date));
        $newLog->save();
        return $newLog;
    }
    
    public function getEventsList($as_array = false, $sort = 'DESC') {
        return Events::getEvents($this->id, $as_array, $sort);
    }

    public static function countDay($date)
    {
        if(date('H', strtotime($date)) >= 15) {
            $date = date('Y-m-d', strtotime($date));
        } else {
            $date = date('Y-m-d', strtotime($date . ' -1 day'));
        }
        $day = static::getLog($date);
        $day->count();
    }

    public function count()
    {
        $servis = Servis::getInstance();
        $events = $this->events;
        $profit = 0;
        foreach ($events as $event) {
            if(in_array($event->result, [0,3])) {
                continue;
            }
            if($event->result == 1) {
                $profit += $event->bank_percent * $event->coefficient;
            }
            $profit -= $event->bank_percent; // 0,02 * 3,17 - 0,02 = 0.0434 = +4,34%
        }
        $this->profit = $servis->beautyDecimal($profit);
        $this->sum_end = $servis->beautyDecimal($this->sum_start + ($this->sum_start * ($this->profit / 100)));
        //$this->comment = $this->countWithComments();
        $this->save();
    }

    public function countWithComments()
    {
        $servis = Servis::getInstance();
        $events = $this->events;
        $profit = 0;
        $report = '';
        foreach ($events as $event) {
            $report .= 'Событие ' . $event->id . ', результат ' .Events::$results[$event->result].'</br>';
            if(in_array($event->result, [0,3])) {
                $report .= 'Результат 0'.'</br><hr>';
                continue;
            }
            $report .= $profit;
            if($event->result == 1) {
                $report .= ' + '.$event->bank_percent .'% * '. $event->coefficient;
                $profit += $event->bank_percent * $event->coefficient;
            }
            $profit -= $event->bank_percent; // 0,02 * 3,17 - 0,02 = 0.0434 = +4,34%
            $report .= ' - '.$event->bank_percent . '% = '. $profit. '%</br><hr>';
        }
        $report .= 'Прибыль за день = '. $servis->beautyDecimal($profit).'%</br>';
        $report .= 'Сумма на начало дня: '. $servis->beautyDecimal($this->sum_start).'</br>';
        $report .= 'Сумма на конец дня: '. $servis->beautyDecimal($this->sum_end).'</br>';
        return $report;
    }

    public static function getTable($is_home = false) {

        $service = Servis::getInstance();


        $query = 'SELECT DISTINCT(YEAR(date_add)) as year, MonTH(date_add) as month FROM `days_log` ORDER BY YEAR(date_add), MonTH(date_add) ASC';
        $moths_array = Yii::$app->db->createCommand($query)->queryAll();

        if($is_home) {
            if(date('H') < 10) {
                $query_date = date('Y-m-d', strtotime(' -2 day'));
            } else {
                $query_date = date('Y-m-d', strtotime(' -1 day'));
            }
        } else {
            $query_date = date('Y-m-d');
            if(date('H') < 15) {
                $query_date = date('Y-m-d', strtotime(' -1 day'));
            }
        }

        $logs = static::find()->where('date_add <= "' . $query_date . '"')->with('events_complete')->orderBy('date_add ASC')->all();
        $graf_months = [
            'name' => [],
            'value' => [],
        ];
        $graf_days = [
            'name' => [],
            'value' => [],
            'values' => []
        ];
        
        $data_days = [];
        $data = [];
        $hu = 100;
        $hundreds = static::find()
                ->where('date_add BETWEEN "' . date('Y-m-d', strtotime($query_date . ' -100 days')) . '" AND "' . date('Y-m-d', strtotime($query_date)) . '"')
                ->andWhere('profit > 0')
                ->orderBy('date_add ASC')
                ->all();
        foreach ($hundreds as $hundred) {
            $hu = $hu + $hu * (($hundred->profit * 0.6) / 100);
        }
        $hu = $hu - 100;
        $data['hundred'] = $service->numberSymbol($hu) . $service->beautyDecimal(abs($hu));

        $prev_month_name = false;
        $prev_month_value = 0;
        $prev_day_value = 0;
        foreach ($logs as $log) {
            $day_array =  static::getLogFromArray($logs, $log->date_add);
            $day_array['events'] = [];
            foreach ($log->events_complete as $event) {
                $event_array = [
                    'result' => $event->result == 0 ? '' : Events::$results[$event->result],
                    'result_class' => null,
                    'title' => $event->title,
                    'bank_percent' => $event->bank_percent.'%',
                    'bet' => $event->bet,
                    'coefficient' => $event->coefficient,
                    'bookmaker' => $event->bookmaker,
                    'free' => $event->free
                ];
                if($event->result == 1) {
                    $event_array['result_class'] = 'up';
                } elseif ($event->result == 2) {
                    $event_array['result_class'] = 'down';
                } elseif($event->result == 3) {
                    $event_array['result_class'] = 'equal'; 
                }
                $day_array['events'][] = $event_array;
            }
            $data_days[$log->id] = $day_array;
            if($log->date_add == $query_date) {

                $data['current'] = $day_array;
            }

            if($log->profit != 0 ){
                $month_date = strftime('%b %g', strtotime($log->date_add));

                if(!in_array($month_date, $graf_months['name'])) {
                    $graf_months['name'][] = $month_date;
                    $graf_months['value'][] = $log->profit;
                } else {
                   $k = array_search($month_date,$graf_months['name']);
                    $graf_months['value'][$k] = $log->profit + $graf_months['value'][$k];
                }
               // $prev_month_value += $log->profit;
                //$prev_month_name = $month_date;
                $graf_days['name'][] = strftime('%d %h', strtotime($log->date_add));
                $prev_day_value = $service->beautyDecimal($prev_day_value + $log->profit);
                $graf_days['value'][] = $prev_day_value;
                $graf_days['values'][] = [
                  //  'x' => strftime('%d %h', strtotime($log->date_add)),
                    'y' => $prev_day_value,
                    'z' => $service->beautyDecimal($log->profit, 2, '.'. '')];
            }
        }
        foreach ($graf_months['value'] as $key => $v) {
            $graf_months['value'][$key] = $service->beautyDecimal($graf_months['value'][$key]);
        }


        $date_months = [];
        foreach ($moths_array as $m){
            $month = [];

            $start_day = intval(date('N', strtotime($m['year'] . '-' . $m['month'] . '-' . '1')));
            if($start_day !== 1){
                for ($i = 1; $i < $start_day; $i++) {
                    $diff = $start_day - $i;
                    $new_el = static::getLogFromArray($logs, date('Y-m-d', strtotime($m['year'] . '-' . $m['month'] . '-' . '1 - ' . $diff . ' days' )));
                    $new_el['additional_class'] = 'next-month';
                    $month[] = $new_el;
                }
            }

            $month_end = date("Y-m-t", strtotime($m['year'] . '-' . $m['month']));
            $i_day = date('Y-m-d', strtotime($m['year'] . '-' . $m['month'] . '-' . '1'));
            do {
                $new_el = static::getLogFromArray($logs, $i_day);
                if($i_day > $query_date) {
                    $new_el['additional_class'] = 'next-month';
                }
                $month[] = $new_el;


                $i_day = date('Y-m-d', strtotime($i_day . ' +1 day'));
            } while ($i_day <= $month_end);



            $end_day = intval(date('N', strtotime($month_end)));
            if($end_day !== 7){
                for ($i = 1; $i <= 7 - $end_day; $i++) {
                    $new_el = static::getLogFromArray($logs, date('Y-m-d', strtotime($month_end . ' + ' . $i . ' days' )));
                    $new_el['additional_class'] = 'next-month';
                    $month[] = $new_el;
                }
            }

            $date_months[strtotime($m['year'] . '-' . $m['month'])] = $month;
        }
        $data['graf_months'] = $graf_months;
        $data['graf_days'] = $graf_days;
        $data['months'] = $date_months;
        $data['days'] = $data_days;
        return $data;
    }

    public static function getLogFromArray($array, $date) {

        $service = Servis::getInstance();
        $new_element = [
            'id' => null,
            'profit' => '-',
            'date_add' => $date,
            'date' => strtotime($date),
            'date_short' => strftime('%e %h', strtotime($date)),
            //     'date_real' => $date,
            'result' => '',
            'symbol' => '',
            'sum' => 0,
            'additional_class' => '',
        ];
        foreach ($array as $log) {
            if($date == $log->date_add) {
                $new_element['id'] = $log->id;
                if(!$log->comment) {
                    $log->getCurrentProfit();
                }
                $new_element['profit'] = $service->numberSymbol($log->profit) . $service->beautyDecimal(abs($log->profit)) . '%';
                if($log->profit > 0) {
                    $new_element['result'] = 'plus';
                } elseif ($log->profit < 0) {
                    $new_element['result'] = 'minus';
                }
                $new_element['symbol'] = $service->numberSymbol($log->profit);
                $new_element['sum'] = $service->beautyDecimal(abs($log->profit));
                return $new_element;
            }
        }
        return $new_element;
    }

    public function getCurrentProfit()
    {
        $servis = Servis::getInstance();

        $h = date('H');
        if($h < 15 AND $h >= 10) {
            return 0;
        }
        return $servis->beautyDecimal($this->profit);
        $events = Events::find()->where(['days_log_id' => $this->id, 'result' => [1,2]])->all();
        $profit = 0;
        foreach ($events as $event) {
            if($event->result == 1) {
                $profit += ($event->bank_percent / 100) * $event->coefficient;
            }
            $profit -= ($event->bank_percent / 100); // 0,02 * 3,17 - 0,02 = 0.0434 = +4,34%
        }
        return $this->profit = $servis->beautyDecimal($profit);
    }

    public static function payMoney() {
        $day = static::getLog(date('Y-m-d', strtotime(' -1 days')));

        $service = Servis::getInstance();
        $day->count();
        $day->refresh();


        if($day->profit == 0) {
            $day->comment = $day->countWithComments();
            $day->save();
            return true;
        }
        $day->comment = $day->getAllComment();
        $profit_percent = $day->profit / 100;
        foreach (User::find()
                    ->select(['*', 'first_deposit_date'])
                     ->leftJoin('(SELECT date_add as first_deposit_date, user_id FROM balance_log WHERE status = 1 AND operation in (0,3) GROUP BY user_id ORDER BY date_add ASC) as bl1 on bl1.user_id = user.id')
                     ->where('user.balance >= 3')
                     ->all() as $user) {
            $profit_sum = $user->balance * $profit_percent;
            if($profit_sum < 0) {
                $real_profit = $profit_sum;
                $bal_log_comment = 'Убыток от инвестирования за ' . $day->date_add;
                BalanceLog::add($user->id, $profit_sum, 5, 1, 0, null, $bal_log_comment);
            } else {
                $real_profit = $user->promo_used ? $profit_sum *  0.7 : $profit_sum * 0.6;
                $bal_log_comment = 'Прибыль от инвестирования за ' . $day->date_add;
                $bal_log_comment .= $user->promo_used ? ' (promo+)': null; 
                BalanceLog::add($user->id, $real_profit, 5, 1, 0, null, $bal_log_comment);
                BalanceLog::add($user->id, $user->promo_used ? $profit_sum *  0.3 : $profit_sum *  0.4, 10, 1, 0, null, 'Гонорар сайта');

                if($user->partner_id) {
                    $company_profit = $profit_sum * 0.4;
                    $comment = 'Отчисления пользователя ' . $user->partner_id . ' за ' . $day->date_add . ', прибыль составила = ' . $service->beautyDecimal($profit_sum);
                    PartnerProgram::Basic_Income(
                        $company_profit,
                        $user->partner_id,
                        $first_deposit_date = $user->first_deposit_date,
                        $user->id,
                        1, 
                        $comment);
                   // $company_profit =
                }

            }
            $user->balance += $real_profit;
            $user->save();
        }
        //добавить расчеты начислений пользователям
        $day->save();
    }

    public function getAllComment() {
        $day = $this;
        $service = Servis::getInstance();
        $comment = $day->countWithComments();

        $comment .= '--------- расчеты ---------</br>';

        if($day->profit == 0) {
          //  $day->save();
            return  $comment;
        }

        $profit_percent = $day->profit / 100;
        foreach (User::find()
                     ->select(['*', 'first_deposit_date'])
                     ->leftJoin('(SELECT date_add as first_deposit_date, user_id FROM balance_log WHERE status = 1 AND operation in (0,3) GROUP BY user_id ORDER BY date_add ASC) as bl1 on bl1.user_id = user.id')
                     ->where('user.balance >= 5')
                     ->all() as $user) {
            $comment .= 'Пользователь ' . $user->id . ' (' . $user->username . ') баланс = ' . $service->beautyDecimal($user->balance). '</br>';

            $profit_sum = $user->balance * $profit_percent;
            $comment .= "Прибыль грязная : $user->balance * $profit_percent = $profit_sum</br>";
            if($profit_sum < 0) {
                $real_profit = $profit_sum;
                $comment .= 'Убыток = ' . $real_profit . "</br>";
                $new_balance = $user->balance + $real_profit;
                $comment .= "<b>Баланс пользователя: $user->balance + $real_profit = $new_balance</b></br>";
            } else {
                $real_profit = $user->promo_used ? $profit_sum *  0.7 : $profit_sum * 0.6;
                $comment .= $user->promo_used ? "Прибыль чистая : $profit_sum *  0.7 " . " = $real_profit" . ' (promo+)' . "</br>" : "Прибыль чистая : $profit_sum *  0.6 " . " = $real_profit" . "</br>";
                $new_balance = $user->balance + $real_profit;
                $comment .= "<b>Баланс пользователя: $user->balance + $real_profit = $new_balance</b></br>";
                $company_profit = $profit_sum * 0.4;
                $comment .= "Гонорар сайта = $profit_sum * 0.4  =  <b>$company_profit</b> </br>";
                if($user->partner_id) {
                    $comment .= PartnerProgram::Basic_Income_With_Comments(
                        $company_profit,
                        $user->partner_id,
                        $first_deposit_date = $user->first_deposit_date,
                        $user->id,
                        1 );
                } else {
                    $comment .= 'У пользователя нет старшего партнера'. "</br>";
                }

            }

            $comment .= '-------------------</br>';
        }
        //добавить расчеты начислений пользователям
      //  $day->save();
        return $comment;
    }
}
