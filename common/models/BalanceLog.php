<?php
namespace common\models;

use common\models\trade\Investment;
use common\models\trade\InvestmentLog;
use common\service\Servis;
use yii\db\ActiveRecord;
use Yii;
use common\service\api\AmoCrm;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property float $summ
 * @property integer $system
 * @property integer $operation
 * @property integer $status
 * @property integer $user_id
 * @property string $comment
 * @property integer $recipient_user_id
 * @property string $hash_payment
 * @property boolean $sms
 * @property integer $payway_id
 * @property date $execution_time
 */
class BalanceLog extends ActiveRecord
{

    const in_processing = 0;
    const done = 1;
    const canceled = 2;
    const in_process = 4;

    public static $status_name = [
        0 => 'В обработке',
        1 => 'Выполнено',
        2 => 'Отменено',
        3 => 'Приостановлено',
        4 => 'Ожидается поступление'
    ];


    const deposit = 0;
    const exit_deposit = 1;
    const transfer = 2;
    const present_admin = 3;
    const bonus = 11;

    public static $operation_name = [
        0 => 'Внесение средств',
        1 => 'Вывод средств',
//        2 => 'Перевод средств',
        3 => 'Внесение средств', //'Подарок администрации',
//        4 => 'Инвестирование',
        5 => 'Прибыль',
//        6 => 'Покрытие долга',
        7 => 'Вывод с партнерского счета',
//        8 => 'Гонорар управляющего от готового решения',
//        9 => 'Продолжение инвестиции в Синергию'
        10 => 'Оплата invest', //
        11 => 'Бонус'
    ];

    const internal_transfer = 0;
    const payeer = 1;
    const perfectmoney = 2;
    const cryptonator = 3;
    const interkassa = 4;
    const overdraft = 5;
    const megatransfer = 9;
    const bankcomat = 11;
    const ultrapays = 12;
    const piastrix = 13;
    const synergy = 14;
    const freekassa = 15;
    const fkassa = 16;
    const freeobmen = 17;
    const payop = 18;
    const payinpayout = 19;

    //учитываю при партнерке если больше 0 значит деньги из вне.
    //при добавлении новой системы это учитывать или править вборку партнерки если нужно
    public static $system = [
        0 => 'Внутренние переводы',
        1 => 'PayEer',
        2 => 'PerfectMoney',
        3 => 'Криптовалюты',
        4 => 'Interkassa',
        5 => 'Овердрафт',
        6 => 'Киви',
        7 => 'Яндекс.Деньги',
        8 => 'Перевод на карту',
        9 => 'Megatransfer',
        10 => 'Advcash',
        11 => 'Bankcomat',
        12 => 'Ultrapays',
        13 => 'Piastrix',
        14 => 'Синергия',
        15 => 'Freekassa',
        16 => 'Fkassa',
        17 => 'FreeObmen',
        18 => 'Payop', 
        19 => 'PayinPayout', 
    ];

    public static $user_min_bal_systems = [0, 1, 2, 3, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19];

    public static $cripto_valute = [
        1 => 'Bitcoin',
        2 => 'Bcash',
        3 => 'Blackcoin',
        4 => 'Bytecoin',
        5 => 'Dash',
        6 => 'Dogecoin',
        7 => 'Emercoin',
        8 => 'Ethereum',
        9 => 'Litecoin',
        10 => 'Monero',
        11 => 'Peercoin',
        12 => 'Primecoin',
        13 => 'Ripple',
        14 => 'Zcash',
    ];

    public static $payways = [
        1 => 'Яндекс Деньги',
        2 => 'Qiwi',
        3 => 'Advcash',
        4 => 'Банковские карты',
        5 => 'Альфа Клик',
        6 => 'Приват24',
        7 => 'Bitcoin',

    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_log';
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

    public static function getPayways()
    {
        return [];
    }


    public static function getPaymentsSystem()
    {
        return [
            6 => 'Qiwi',
            7 => Yii::t('cab', 'Яндекс.Деньги'),
            1 => 'Payeer',
            2 => 'Perfect Money',
            8 => Yii::t('cab', 'Перевод на карту'),
            10 => 'Advcash',
        ];
    }

    public static function getPaymentsCommissions()
    {

        return [
            6 => 3,
            7 => 3,
            1 => 3,
            2 => 3,
            8 => 5,
            10 => 3,
        ];

    }

    public static function getAdminChangeTranserOperations()
    {
        $not_array = [4];
        $return = [];
        foreach (static::$status_name as $key => $op) {
            if (!in_array($key, $not_array)) {
                $return[$key] = $op;
            }
        }
        return $return;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
//        $user = User::findIdentity($this->user_id);
//        if (!$user OR !$user->amo_contact_id) {
//            AmoCrm::getInstance()->updateUser($user, ['balance']);
//        }
    }


    public static function getOperationNames()
    {
        if (Yii::$app->user->can('admin')) {
            return static::$operation_name;
        } else {
            return [
                1 => 'Вывод средств',
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function addLog($user_id, $summ, $operation, $status, $system = 0, $date_add = null, $comment = null, $recipient_user = null, $hash = null, $sms = false, $payway_id = null)
    {

        $this->user_id = $user_id;
        $this->summ = $summ;
        $this->operation = $operation;
        $this->status = $status;
        $this->system = $system;
        $this->comment = $comment;
        $this->recipient_user_id = $recipient_user;
        $this->hash_payment = $hash;
        $this->sms = $sms;
        $this->payway_id = $payway_id;
        if (!$date_add) {
            $date_add = date("Y-m-d H:i:s");
        }
        if ($status != 4) {
            $this->execution_time = $date_add;
        }
        $this->date_add = $date_add;
    }

    public static function add($user_id, $summ, $operation, $status, $system = 0, $date_add = null, $comment = null, $recipient_user = null, $hash = null, $sms = false, $payway_id = null)
    {
        $log = new static();
        $log->addLog($user_id, $summ, $operation, $status, $system = 0, $date_add, $comment, $recipient_user, $hash, $sms, $payway_id);
        $log->save();
    }

    /**
     * @param $user_id
     * @param bool $sum
     * @return array|ActiveRecord[]
     */
    public static function getMonthlyContributions($user_id, $sum = true, $mount = true)
    {

        $sql = 'user_id = ' . $user_id .
            ' AND ( operation = ' . self::deposit . ' OR operation = ' . self::transfer . ' OR  operation = ' . self::present_admin . ' )' .
            ' AND system != ' . self::overdraft;

        if ($mount) {
            $date = Servis::getInstance()->getDateFerstDayNowMount();
            $sql .= ' AND date_add >= "' . $date . '"';
        }

        $t = BalanceLog::find()
            ->where($sql)
            ->all();

        if ($sum) {
            if (empty($t)) {
                return 0;
            }

            $out = 0;
            foreach ($t as $item) {
                if ($item->summ > 0) {
                    $out += $item->summ;
                }
            }
            return $out;
        } else {
            return $t;
        }
    }

    /**
     * подсчет привлеченный капитал для пункта 3 по партнерке
     */
    public static function getMonthlyContributionsP3($user_ids)
    {

        if (empty($user_ids)) {
            $user_ids = 0;
        }

        $ids = explode(',', $user_ids);

        $date = Servis::getInstance()->getDateFerstDayNowMountMy();

        $summ = array();
        foreach ($ids as $id) {
            $sql = '( user_id =  ' . $id .
                ' AND ( operation = ' . BalanceLog::deposit . ' OR operation = ' . BalanceLog::transfer . ' OR  operation = ' . BalanceLog::present_admin . ' )' .
                ' AND system != ' . BalanceLog::overdraft .
                ')';

            $sql .= ' AND date_add >= "' . $date . '"';

            $add = BalanceLog::find()
                ->select(' sum( summ ) as summ')
                ->where($sql)
                ->createCommand()
                ->queryScalar();
            $add = ($add) ? $add : 0;
            $add = ($add > 0) ? $add : 0;

            $sql = '( user_id = ' . $id .
                ' AND ( operation = ' . BalanceLog::exit_deposit . ' )' .
                ' AND system != ' . BalanceLog::overdraft .
                ' AND status = 1' .
                ')';

            $sql .= ' AND date_add >= "' . $date . '"';

            $exit = BalanceLog::find()
                ->select(' sum( summ ) as summ')
                ->where($sql)
                ->createCommand()
                ->queryScalar();

            $exit = ($exit) ? ($exit * (-1)) : 0;
            $exit = ($exit > 0) ? $exit : 0;

            $exit = $add - $exit;
            $exit = ($exit > 0) ? $exit : 0;

            $summ[$id] = $exit;
        }

        $out = 0;
        foreach ($summ as $item)
            $out += $item;
        return $out;


    }

    /**
     * Сумма которая сейчас в Ду м Готовом решении - овердрафт
     * $is_invest - если для инвестора другие поля берем
     */
    public static function getMonthlyContributionsP2($user_ids, $is_invest = false)
    {

        if (empty($user_ids)) {
            $user_ids = 0;
        }
        $user_ids_tmp = $user_ids;
        $user_ids = explode(',', $user_ids);
        $summ = array();

        foreach ($user_ids as $id) {

            $sql_invest = '( user_id  = ' . $id .
                ' AND ( bonus_money is null )' .
                ')';


            $invest = Investment::find()
                ->where($sql_invest)
                ->andWhere(['deleted' => false])
                ->all();
            //получим массив ключ ид инвестиции = сумма инвестирования
            foreach ($invest as $item) {
                if ($item->solution_id) {
                    if ($is_invest) {
                        $summ[$item->id] = (isset($summ[$item->id])) ? ($item->getSummShow() + $summ[$item->id]) : $item->getSummShow();
                    } else {
                        $summ[$item->id] = (isset($summ[$item->id])) ? ($item->summ + $summ[$item->id]) : $item->getSummShow();
                    }
                } else {
                    $ta = $item->account;
                    if ($ta->is_du && $ta->user_id != $id) {
                        if ($is_invest) {
                            $summ[$item->id] = (isset($summ[$item->id])) ? ($item->getSummShow() + $summ[$item->id]) : $item->getSummShow();
                        } else {
                            $summ[$item->id] = (isset($summ[$item->id])) ? ($item->summ + $summ[$item->id]) : $item->getSummShow();
                        }
                    }
                }
            }


        }

        //отнимим овердрафт
        $sql_overdraft = '( user_id IN ( ' . $user_ids_tmp . ')' .
            ' AND ( is_dolg = 1 )' .
            ')';


        $overdraft = Overdraft::find()
            ->select(' sum( summ ) as summ')
            ->where($sql_overdraft)
            ->createCommand()
            ->queryScalar();
        $overdraft = ($overdraft) ? $overdraft : 0;

        $res = 0;
        foreach ($summ as $item) {
            $res += $item;
        }
        $res = $res - $overdraft;
        $res = ($res < 0) ? 0 : $res;

        return $res;

    }

    public static function getMonthlyContributionsFerstLine($user_ids, $mount = true)
    {

        if (empty($user_ids)) {
            $user_ids = 0;
        }

        $date = Servis::getInstance()->getDateFerstDayNowMountMy();

        //считаем средства прошедшие через платежные с-мы
        $sql = '( user_id IN ( ' . $user_ids . ')' .
            ' AND ( operation = ' . self::deposit . ' OR operation = ' . self::transfer . ' OR  operation = ' . self::present_admin . ' )' .
            ' AND system != ' . self::overdraft .
            ')';

        if ($mount)
            $sql .= ' AND date_add >= "' . $date . '"';

        $out = BalanceLog::find()
            ->select(' sum( summ ) as summ')
            ->where($sql)
            ->createCommand()
            ->queryScalar();
        $out = ($out) ? $out : 0;

        $user_ids = explode(',', $user_ids);
        $summ = array();

        foreach ($user_ids as $id) {

            $sql_invest = '( user_id  = ' . $id .
                ' AND ( balance_bonus_log_id is null AND type = 1 AND status = 1 )' .
                ')';

            $sql_invest_out = '( user_id = ' . $id .
                ' AND ( balance_bonus_log_id is null AND type = 2 AND status = 1 )' .
                ')';

            if ($mount) {
                $sql = '( user_id =  ' . $id .
                    ' AND ( operation = ' . self::deposit . ' OR operation = ' . self::transfer . ' OR  operation = ' . self::present_admin . ' )' .
                    ' AND system != ' . self::overdraft .
                    ')';

                $sql .= ' AND date_add >= "' . $date . '"';
                $sql_invest .= ' AND datetime_add >= "' . $date . '"';
                $sql_invest_out .= ' AND datetime_add >= "' . $date . '"';

                $popolnil = BalanceLog::find()
                    ->select(' sum( summ ) as summ')
                    ->where($sql)
                    ->createCommand()
                    ->queryScalar();
                $popolnil = ($popolnil) ? $popolnil : 0;

                if (!$popolnil) {
                    continue;
                }

            }

            $invest = InvestmentLog::find()
                ->where($sql_invest)
                ->all();
            //получим массив ключ ид инвестиции = сумма инвестирования
            foreach ($invest as $item) {
                if ($item->solution_id) {
                    $summ[$item->investment_id] = (isset($summ[$item->investment_id])) ? ($item->summ + $summ[$item->investment_id]) : $item->summ;
                } else {
                    $ta = $item->account;
                    if ($ta->is_du && $ta->user_id != $id) {
                        $summ[$item->investment_id] = (isset($summ[$item->investment_id])) ? ($item->summ + $summ[$item->investment_id]) : $item->summ;
                    }
                }
            }

            $invest_out = InvestmentLog::find()
                ->where($sql_invest_out)
                ->all();

            //отнимим выведенные средства если прибыль обнулим
            foreach ($invest_out as $item) {
                if (isset($summ[$item->investment_id])) {
                    $summ[$item->investment_id] = $summ[$item->investment_id] - $item->summ;

                    if ($summ[$item->investment_id] < 0) {
                        $summ[$item->investment_id] = 0;
                    }

                    if ($mount) {
                        if ($popolnil < $summ[$item->investment_id]) {
                            $summ[$item->investment_id] = $popolnil;
                        }
                    }
                }
            }
        }

        $res = 0;
        foreach ($summ as $item) {
            $res += $item;
        }


        //проверим суммы и не позволим записать больше чем пополнили через платужную с-му
        if ($res < 0) {
            $out = 0;
        } else if ($res <= $out) {
            $out = $res;
        }

        return $out;

    }

    public static function getCountLowerPartners($user_id, $status)
    {

        $sql = 'partner_id = ' . $user_id . ' AND status_in_partner >= ' . ($status);

        return User::find()
            ->where($sql)
            ->count();
    }

}
