<?php

namespace common\service;

use common\models\Options;
use common\models\trade\InvestmentDailyLog;
use common\models\trade\TradingAccount;
use DateTime;
use Yii;
use yii\db\Query;
use yii\helpers\Url;

if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}

class Servis
{
    private static $instance = null;

    private $id_vk = null;
    private $secret_vk = null;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->id_vk = \Yii::$app->params['id_vk'];
            self::$instance->secret_vk = \Yii::$app->params['secret_vk'];
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    public function rusToLat($title)
    {
        $chars = array(
//rus
            "А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D",
            "Е" => "E", "Ё" => "YO", "Ж" => "ZH",
            "З" => "Z", "И" => "I", "Й" => "Y", "К" => "K", "Л" => "L",
            "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
            "С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "KH",
            "Ц" => "C", "Ч" => "CH", "Ш" => "SH", "Щ" => "SHH", "Ъ" => "",
            "Ы" => "Y", "Ь" => "", "Э" => "YE", "Ю" => "YU", "Я" => "YA",
            "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d",
            "е" => "e", "ё" => "yo", "ж" => "zh",
            "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
            "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
            "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "kh",
            "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "shh", "ъ" => "",
            "ы" => "y", "ь" => "", "э" => "ye", "ю" => "yu", "я" => "ya",
//spec
            "—" => "_", "«" => "", "»" => "", "…" => "", "№" => "N",
            "—" => "_", "«" => "", "»" => "", "…" => "",
            "!" => "", "@" => "", "#" => "", "$" => "", "%" => "", "^" => "", "&" => "",
            " " => "_",
//ukr
            "Ї" => "Yi", "ї" => "i", "Ґ" => "G", "ґ" => "g",
            "Є" => "Ye", "є" => "ie", "І" => "I", "і" => "i",
//kazakh
            "Ә" => "A", "Ғ" => "G", "Қ" => "K", "Ң" => "N", "Ө" => "O", "Ұ" => "U", "Ү" => "U", "H" => "H",
            "ә" => "a", "ғ" => "g", "қ" => "k", "ң" => "n", "ө" => "o", "ұ" => "u", "h" => "h"
        );

        $title = preg_replace('/\.+/', '.', $title);
        $r = strtr($title, $chars);

        return $r;
    }

    public function getDateTo($date, $year = false)
    {
        $mounts = ['', 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'];
        $mounts_en = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',];
        $date = strtotime($date);
        $m = date("n", $date);
        $d = date("d", $date);
        $y = date("Y", $date);

        if (Yii::$app->language == 'ru') {
            $return = ($year) ? $d . ' ' . $mounts[$m] . ' ' . $y : $d . ' ' . $mounts[$m];
        } else {
            $return = ($year) ? $d . ' ' . $mounts_en[$m] . ' ' . $y : $d . ' ' . $mounts_en[$m];
        }
        return $return;
    }

    public function getDateIpLog($date)
    {
        $mounts = ['', 'янв.', 'февр.', 'март.', 'апр.', 'мая.', 'июня.', 'июля.', 'авг.', 'сент.', 'октя.', 'нояб.', 'дек.'];
        $date = strtotime($date);
        $m = date("n", $date);
        $d = date("d", $date);
        return $d . ' ' . $mounts[$m] . date(' Y г., H:i:s', $date);
    }

    public function randomCode($length = 2, $onlyNumbers = false)
    {
        $chars = $onlyNumbers ? '1234567890' : 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

    public function getDateWord($date)
    {
        $days = ['', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'];
        $date = strtotime($date);
        $today = new DateTime();
        $today->setTime(0, 0, 0);

        $match_date = DateTime::createFromFormat("Y.m.d\\TH:i", date("Y.m.d\\TH:i", $date));
        $match_date->setTime(0, 0, 0);
        $diff = $today->diff($match_date);
        $deys = (integer)$diff->format("%R%a");
        $out = '';

        switch ($deys) {
            case 0:
                $out = Yii::t('cab', "Сегодня");
                break;
            case -1:
                $out = Yii::t('cab', "Вчера");
                break;
            case -2:
            case -3:
            case -4:
            case -5:
            case -6:
                $out = Yii::$app->language == 'ru' ? $days[date("N", $date)] : strftime("%A", $date);
                break;
            default:
                $out = strftime("%d %B %Y", $date);
        }

        return $out;
    }

    public static function tradingAccountLifetime($created_at)
    {
        $return = '';
        $now = strtotime('now');
        $date = strtotime($created_at);
        $lifetime = $now - $date;
        $monthes = ($lifetime - $lifetime % 2592000) / 2592000;
        switch ($monthes % 10) {
            case 1:
                $month_word = "месяц";
                break;
            case 2:
            case 3:
            case 4:
                $month_word = "месяца";
                break;
            default:
                $month_word = "месяцев";
        }
        $return = $monthes != 0 ? '<span class="blue-numbers">' . $monthes . '</span>' . ' ' . $month_word . ', ' : '';

        $weeks = ($lifetime % 2592000 - ($lifetime % 2592000) % 604800) / 604800;
        switch ($weeks) {
            case 1:
                $week_word = " неделя";
                break;
            default:
                $week_word = " недели";
        }
        $return = ($weeks != 0) ? $return . '<span class="blue-numbers">' . $weeks . '</span>' . $week_word : $return . '';

        if ($monthes == 0) {
            $days = ((($lifetime % 2592000) % 604800) - (($lifetime % 2592000) % 604800) % 86400) / 86400;
            switch ($days) {
                case 0:
                    $day_word = " дней";
                    break;
                case 1:
                    $day_word = " день";
                    break;
                case 2:
                case 3:
                case 4:
                    $day_word = " дня";
                    break;
                default:
                    $day_word = " дней";
            }
            if ($days > 0 AND $weeks > 0) {
                $return = $return . ', ' . '<span class="blue-numbers">' . $days . '</span>' . $day_word;
            } elseif ($weeks == 0) {
                $return = '<span class="blue-numbers">' . $days . '</span>' . $day_word;
            }

        }

        return $return; //$return;
    }

    public static function getDateLifetime($created_at)
    {
        $return = '';
        if (date('Y-m-d', strtotime($created_at)) == date('Y-m-d')) {
            return Yii::t('app', "сегодня");
        }
        $now = strtotime('now');
        $date = strtotime($created_at);
        $lifetime = $now - $date;
        $monthes = ($lifetime - $lifetime % 2592000) / 2592000;
        switch ($monthes % 10) {
            case 1:
                $month_word = Yii::t('app', "месяц назад");
                break;
            case 2:
            case 3:
            case 4:
                $month_word = Yii::t('app', "месяца назад");
                break;
            default:
                $month_word = Yii::t('app', "месяцев назад");
        }
        $return = $monthes != 0 ? $monthes . ' ' . $month_word : '';
        if ($return) {
            return $return;
        }

        $weeks = ($lifetime % 2592000 - ($lifetime % 2592000) % 604800) / 604800;
        switch ($weeks) {
            case 1:
                $week_word = Yii::t('app', "неделя назад");
                break;
            default:
                $week_word = Yii::t('app', "недели назад");
        }
        $return = ($weeks != 0) ? $weeks . ' ' . $week_word : '';
        if ($return) {
            return $return;
        }

        if ($monthes == 0) {
            $days = ((($lifetime % 2592000) % 604800) - (($lifetime % 2592000) % 604800) % 86400) / 86400;
            switch ($days) {
                case 0:
                   return Yii::t('app', "вчера");
                
                case 1:
                    $day_word = Yii::t('app', "вчера");
                    break;
                case 2:
                case 3:
                case 4:
                    $day_word = Yii::t('app', "дня назад");
                    break;
                default:
                    $day_word = Yii::t('app', "дней назад");
            }

            $return = $days . ' ' . $day_word;
        }
        return $return;
    }


    public static function getDaysToPeriod($days)
    {
        if($days > 300) {
            return Yii::t('app', 'Больше 10 месяцев');
        }
        $months = floor($days / 30);
        $weeks = floor(($days - $months * 30) / 7);
        $days = $days - $months * 30 - $weeks * 7;


        $str = '';
        $rows = 0;
        if ($months > 0) {
            $str .= static::declension($months, array('месяц', 'месяца', 'месяцев')) . '</br>';
            $rows++;
        }
        if($weeks == 0 AND $rows == 1) {
            return $str;
        }
        $str .= static::declension($weeks, array('неделя', 'недели', 'недель')) . '</br>';
        $rows++;

        if ($days > 0 AND  $rows < 2) $str .= static::declension($days, array('день', 'дня', 'дней'));
        return $str;
    }

    public static function declension($digit,$expr,$onlyword=false){
	        if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
	        if(empty($expr[2])) $expr[2]=$expr[1];
	        $i=preg_replace('/[^0-9]+/s','',$digit)%100;
	        if($onlyword) $digit='';
	        if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
	        else
	        {
	            $i%=10;
	            if($i==1) $res=$digit.' '.$expr[0];
	            elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
	            else $res=$digit.' '.$expr[2];
	        }
	        return trim($res);
	    }

    public function translete($model)
    {
        $lang = Yii::$app->language;
        if ($lang == 'ru') {
            return $model;
        }
        $lang = '_' . $lang;

        $arr = array();
        foreach ($model as $key => $value) {
            if ((substr($key, -(strlen($lang)))) !== $lang) {
                $arr[$key] = $key . $lang;
            }
        }

        $out = new \stdClass();
        foreach ($arr as $key => $value) {
            $out->$key = (!empty($model->$value)) ? $model->$value : $model->$key;
        }

        return $out;
    }

    public function daysToDate($date)
    {
        $now = time();
        $your_date = strtotime($date);
        $datediff = $your_date - $now;
        $days = floor($datediff / (60 * 60 * 24));
        return $days;
    }

    public function wordOfDays($days)
    {
        $day_word = 'дней';
        switch ($days) {
            case 0:
                $day_word = Yii::t('cab', " дней");
                break;
            case 1:
                $day_word = Yii::t('cab', " день");
                break;
            case 2:
            case 3:
            case 4:
                $day_word = Yii::t('cab', " дня");
                break;
            default:
                $day_word = Yii::t('cab', " дней");
        }

        return $day_word;
    }


    public function tradeAccountLines($profit, $invested)
    {
        $base = 30;
        $max = 62;
        $max_percent = $max / 100;
        $return = [
            'base' => $base,
            'second_base' => $base,
            'profit' => 1,
        ];
        if ($invested <= 0) {
            return $return = [
                'base' => 1,
                'second_base' => 1,
                'profit' => 1,
            ];
        }
        switch (true) {
            case $profit == 0:
                break;
            case $profit >= 49000:
                $return = [
                    'base' => 1,
                    'second_base' => 1,
                    'profit' => 49,
                ];
                break;
            case $profit < -100:
                $return = [
                    'base' => $base,
                    'second_base' => 1,
                    'profit' => 1,
                ];
                break;
            case ($profit > 0) AND ($profit < 163):
                $return = [
                    'base' => $base,
                    'second_base' => $base,
                    'profit' => ceil($profit * 0.3) + 1,
                ];
                break;
            case $profit > 163:
                $summ = $profit + 100;
                $base = floor(100 / ($summ / 100) * $max_percent);
                $return = [
                    'base' => $base,
                    'second_base' => $base,
                    'profit' => $max - $base,
                ];
                break;
            case ($profit < 0) AND ($profit > -100):
                $return = [
                    'base' => $base,
                    'second_base' => floor($base + $profit * ($base / 100)),
                    'profit' => 1,
                ];
                break;
        }
        return $return;
    }

    public function getDateFerstDayNowMount()
    {
        $m = date('n');
        $y = date('y');
        $d = date('d');

        if ($d == '01') {
            if ($m == 1) {
                $m == 12;
            } else {
                $m = $m - 1;
            }
        }
        $m = ($m > 9) ? $m : '0' . $m;

        $date = new DateTime($y . '-' . $m . '-01');
        return $date->format('Y-m-d H:i:s');
    }

    public function getDateFerstDayNowMountMy()
    {
        //todo delete
//		return '2018-05-01 00:00:00';
        return date('Y-m-01 00:00:00');
    }

    public function getDateLastDayNowMount()
    {
        $m = date('n');
        $y = date('y');


        $m = ($m == 12) ? '01' : $m + 1;
        $m = ($m > 9) ? $m : '0' . $m;

        $date = strtotime($y . '-' . $m . '-01');
        return date('d.m', $date - 7200);
    }

    /**
     * партнерка масив вознаграждений за привлеченный капитал
     * @return array
     */
    public function getArrBonusesForMonth()
    {

        return [
            ['summ' => '300000', 'bonus' => '50 000'],
            ['summ' => '100000', 'bonus' => '20 000'],
            ['summ' => '50000', 'bonus' => '10 000'],
            ['summ' => '20000', 'bonus' => '5 000'],
            ['summ' => '10000', 'bonus' => '2 500'],
            ['summ' => '5000', 'bonus' => '1 000'],
            ['summ' => '3000', 'bonus' => '500'],
            ['summ' => '1000', 'bonus' => '100'],
        ];
    }

    /**
     * партнерка масив получений нового статуса
     * @return array
     */
    public function getArrChangeStatus()
    {
        return [
            0 => ['status' => 'Нет статуса', 'capital' => 0, 'partner' => ['count' => 0, 'status' => 0], 'personal_funds' => 0, 'bonus' => ['premiya' => 0, 'line' => []]],
            1 => ['status' => 'Консультант', 'capital' => 500, 'partner' => ['count' => 0, 'status' => 0], 'personal_funds' => 0, 'bonus' => ['premiya' => 50, 'line' => []]],
            2 => ['status' => 'Специалист', 'capital' => 1500, 'partner' => ['count' => 2, 'status' => 1], 'personal_funds' => 0, 'bonus' => ['premiya' => 150, 'line' => [2 => 5]]],
            3 => ['status' => 'Профессионал', 'capital' => 5000, 'partner' => ['count' => 2, 'status' => 2], 'personal_funds' => 100, 'bonus' => ['premiya' => 500, 'line' => [2 => 5, 3 => 1]]],
            4 => ['status' => 'Эксперт', 'capital' => 15000, 'partner' => ['count' => 2, 'status' => 3], 'personal_funds' => 150, 'bonus' => ['premiya' => 1500, 'line' => [2 => 5, 3 => 1]]],
            5 => ['status' => 'Топ-эксперт', 'capital' => 30000, 'partner' => ['count' => 2, 'status' => 4], 'personal_funds' => 500, 'bonus' => ['premiya' => 3000, 'line' => [2 => 5, 3 => 1, 4 => 1]]],
            6 => ['status' => 'Лидер', 'capital' => 45000, 'partner' => ['count' => 2, 'status' => 5], 'personal_funds' => 1000, 'bonus' => ['premiya' => 4500, 'line' => [2 => 5, 3 => 1, 4 => 1]]],
            7 => ['status' => 'Региональный представитель', 'capital' => 80000, 'partner' => ['count' => 2, 'status' => 6], 'personal_funds' => 1500, 'bonus' => ['premiya' => 10000, 'line' => [2 => 5, 3 => 1, 4 => 1, 5 => 0.5]]],
            8 => ['status' => 'Премиум-партнёр', 'capital' => 150000, 'partner' => ['count' => 2, 'status' => 7], 'personal_funds' => 3000, 'bonus' => ['premiya' => 0, 'line' => [2 => 5, 3 => 1, 4 => 1, 5 => 0.5]]],
            9 => ['status' => 'VIP-партнёр', 'capital' => 500000, 'partner' => ['count' => 2, 'status' => 8], 'personal_funds' => 5000, 'bonus' => ['premiya' => 0, 'line' => [2 => 5, 3 => 1, 4 => 1, 5 => 0.5, 6 => 0.2, 7 => 0.1]]],
        ];
    }

    public function getChangeStatusInfo()
    {
        return [
            0 => [
                'capital' => 0,
                'title' => '',
                'bonus' => 0,
                'cashback' => 20,
                'description' => '',
                'img' => '',
            ],
            1 => [
                'capital' => 1000,
                'title' => Yii::t('app', 'Получи') . ' 25$',
                'bonus' => 25,
                'cashback' => 5,
                'description' => Yii::t('app', '25$ и дополнительный кэшбэк 5% с приглашённых второго уровня'),
                'img' => '/img/step-1.png',
            ],
            2 => [
                'capital' => 5000,
                'title' => Yii::t('app', 'Получи') . ' 150$',
                'bonus' => 150,
                'cashback' => 3,
                'description' => Yii::t('app', '150 и дополнительный кэшбэк 3% с приглашённых третьего уровня'),
                'img' => '/img/step-2.png',
            ],
            3 => [
                'capital' => 25000,
                'title' => Yii::t('app', 'Получи') . ' 1000$',
                'bonus' => 1000,
                'cashback' => 10,
                'description' => Yii::t('app', '1000$ и дополнительный кэшбэк 1% с приглашённых четвёртого уровня'),
                'img' => '/img/step-3.png',
            ],
            4 => [
                'capital' => 100000,
                'title' => Yii::t('app', 'Получи MacBook и 2000$'),
                'bonus' => 2000,
                'cashback' => 0,
                'description' => null,
                'img' => '/img/step-4.png',
            ],
            5 => [
                'capital' => 300000,
                'title' => Yii::t('app', 'Открой франшизу'),
                'bonus' => 10000,
                'cashback' => 0,
                'description' => Yii::t('app', 'Франшизу invest в своём городе и дополнительно 10 000$'),
                'img' => '/img/step-5.png',
            ],
        ];
    }


    public function getStatusLines($count = 0)
    {
        $status_lines = [
            '<span>30</span>% ' . Yii::t('cab', "по 1-й линии"),
            '<span>5</span>% ' . Yii::t('cab', "по 2-й линии"),
            '<span>1</span>% ' . Yii::t('cab', "по 3-й линии"),
            '<span>1</span>% ' . Yii::t('cab', "по 4-й линии"),
            '<span>0.5</span>% ' . Yii::t('cab', "по 5-й линии"),
            '<span>0.2</span>% ' . Yii::t('cab', "по 6-й линии"),
            '<span>0.1</span>% ' . Yii::t('cab', "по 7-й линии")];
        $return = [];
        for ($i = 1; $i <= $count; $i++) {
            $return[] = $status_lines[$i - 1];
        }
        return $return;
    }

    public function getArrPartnerStatus()
    {
        $return['statuses'] = [
            9 => ['status' => Yii::t('cab', 'VIP-партнёр'), 'capital' => '500 000 $', 'partner' => '2 <span>' . Yii::t('cab', 'Премиум-партнёр') . '</span>', 'personal_funds' => '5 000  $', 'premiya' => ['<div class="description__premiya">' . Yii::t('cab', 'Чек на покупку недвижимости номиналом') . ' <span>100 000$</span></div>'], 'line' => $this->getStatusLines(7)],
            8 => ['status' => Yii::t('cab', 'Премиум-партнёр'), 'capital' => '150 000 $', 'partner' => '2 <span>' . Yii::t('cab', 'Региональный представитель') . '</span>', 'personal_funds' => '3 000 $', 'premiya' => ['<div class="description__premiya">' . Yii::t('cab', 'Новый Volkswagen Touareg Status') . '</div>'], 'line' => $this->getStatusLines(5)],
            7 => ['status' => Yii::t('cab', 'Региональный представитель'), 'capital' => '80 000 $', 'partner' => '2 <span>' . Yii::t('cab', 'Лидер') . '</span>', 'personal_funds' => '1 500 $', 'premiya' => ['10 000 <i>$</i>', '<div class="description__premiya">' . Yii::t('cab', 'Открытие офиса за счёт компании.') . '</div>'], 'line' => $this->getStatusLines(5)],
            6 => ['status' => Yii::t('cab', 'Лидер'), 'capital' => '45 000 $', 'partner' => '2 <span>Топ-' . Yii::t('cab', 'Топ-эксперт') . '</span>', 'personal_funds' => '1 000 $', 'premiya' => ['4 500 <i>$</i>', '<div class="description__premiya">' . Yii::t('cab', 'Проведение рекламной кампании на') . ' <span>1 000$</span></div>'], 'line' => $this->getStatusLines(4)],
            5 => ['status' => Yii::t('cab', 'Топ-эксперт'), 'capital' => '30 000 $', 'partner' => '2 <span>' . Yii::t('cab', 'Эксперт') . '</span>', 'personal_funds' => '500 $', 'premiya' => ['3 000 <i>$</i>'], 'line' => $this->getStatusLines(4)],
            4 => ['status' => Yii::t('cab', 'Эксперт'), 'capital' => '15 000 $', 'partner' => '2 <span>' . Yii::t('cab', 'Профессионал') . '</span>', 'personal_funds' => '150 $', 'premiya' => ['1 500 <i>$</i>'], 'line' => $this->getStatusLines(3)],
            3 => ['status' => Yii::t('cab', 'Профессионал'), 'capital' => '5 000 $', 'partner' => '2 <span>' . Yii::t('cab', 'Специалист') . '</span>', 'personal_funds' => '100 $', 'premiya' => ['500 <i>$</i>'], 'line' => $this->getStatusLines(3)],
            2 => ['status' => Yii::t('cab', 'Специалист'), 'capital' => '1 500 $', 'partner' => '2 <span>' . Yii::t('cab', 'Консультант') . '</span>', 'personal_funds' => '', 'premiya' => ['150 <i>$</i>'], 'line' => $this->getStatusLines(2)],
            1 => ['status' => Yii::t('cab', 'Консультант'), 'capital' => '500 $', 'partner' => '', 'personal_funds' => '', 'premiya' => ['50 <i>$</i>'], 'line' => $this->getStatusLines(1)],
        ];
        if (!Yii::$app->user->isGuest) {
            $return['level'] = Yii::$app->user->identity->status_in_partner;

        }
        return $return;
    }

    /**
     * масив условий вознаграждения по инвестированиям
     */
    public function getArrAttractionInvestors()
    {
        return [
            1 => ['investoriv' => 50, 'ball' => 300],
            2 => ['investoriv' => 100, 'ball' => 600],
            3 => ['investoriv' => 300, 'ball' => 2000],
            4 => ['investoriv' => 500, 'ball' => 4000],
            5 => ['investoriv' => 1000, 'ball' => 8000],
        ];
    }

    /**
     * масив условий вознаграждения по статусам
     */
    public function getArrAttractionPartner()
    {
        return [
            1 => ['count' => 5, 'ball' => 100],
            2 => ['count' => 10, 'ball' => 300],
            3 => ['count' => 15, 'ball' => 500],
            4 => ['count' => 30, 'ball' => 1000],
            5 => ['count' => 50, 'ball' => 2000],
        ];
    }

    public function getArrDepozit()
    {
        return [
            1 => ['summ' => 500, 'ball' => 30],
            2 => ['summ' => 1000, 'ball' => 100],
            3 => ['summ' => 5000, 'ball' => 500],
            4 => ['summ' => 10000, 'ball' => 1500],
        ];
    }

    public function getArrChangeBall()
    {
        return [
            'min' => 500,
            'max' => 5000
        ];
    }

    public function numberSymbol($number, $without_decimal = false)
    {
        if ($without_decimal) {
            $number = number_format($number, 0, '.', '');

        }
        if ($number == 0) {
            return '';
        }
        return $number < 0 ? '-' : '+';
    }

    public function beautyProfit($number, $abs = true)
    {
        if ($abs) {
            $number = abs($number);
        }
        $number = number_format($number, $number - floor($number) ? 2 : 0, '.', ' ');
        return $number == 0 ? 0 : $number;
    }


    public function beautyDecimal($number, $elementAfterSeparator = 2, $separator = '.', $thousand = '')
    {
        $floor = $number - floor($number);
        if ($elementAfterSeparator AND $floor AND $floor >= 0.01) {
            $arr = [
                1 => 10,
                2 => 100,
                3 => 1000,
                4 => 10000,
            ];
            if (isset($arr[$elementAfterSeparator])) {
                $number = floor($number * $arr[$elementAfterSeparator]);
                $number = $number / $arr[$elementAfterSeparator];
            }

            $number = rtrim(number_format($number, $elementAfterSeparator, $separator, $thousand), 0);
        } else {
            $number = number_format($number, 0, $separator, $thousand);
        }
        return $number;
    }

    public static function cropImage($aInitialImageFilePath, $aNewImageFilePath, $aNewImageWidth = 300, $aNewImageHeight = 300)
    {
        if (($aNewImageWidth < 0) || ($aNewImageHeight < 0)) {
            return false;
        }

        // Массив с поддерживаемыми типами изображений
        $lAllowedExtensions = array(1 => "gif", 2 => "jpeg", 3 => "png");

        // Получаем размеры и тип изображения в виде числа
        list($lInitialImageWidth, $lInitialImageHeight, $lImageExtensionId) = getimagesize($aInitialImageFilePath);

        if (!array_key_exists($lImageExtensionId, $lAllowedExtensions)) {
            return false;
        }
        $lImageExtension = $lAllowedExtensions[$lImageExtensionId];

        // Получаем название функции, соответствующую типу, для создания изображения
        $func = 'imagecreatefrom' . $lImageExtension;
        // Создаём дескриптор исходного изображения
        $lInitialImageDescriptor = $func($aInitialImageFilePath);

        // Определяем отображаемую область
        $lCroppedImageWidth = 0;
        $lCroppedImageHeight = 0;
        $lInitialImageCroppingX = 0;
        $lInitialImageCroppingY = 0;
        if ($aNewImageWidth / $aNewImageHeight > $lInitialImageWidth / $lInitialImageHeight) {
            $lCroppedImageWidth = floor($lInitialImageWidth);
            $lCroppedImageHeight = floor($lInitialImageWidth * $aNewImageHeight / $aNewImageWidth);
            $lInitialImageCroppingY = floor(($lInitialImageHeight - $lCroppedImageHeight) / 2);
        } else {
            $lCroppedImageWidth = floor($lInitialImageHeight * $aNewImageWidth / $aNewImageHeight);
            $lCroppedImageHeight = floor($lInitialImageHeight);
            $lInitialImageCroppingX = floor(($lInitialImageWidth - $lCroppedImageWidth) / 2);
        }

        // Создаём дескриптор для выходного изображения
        $lNewImageDescriptor = imagecreatetruecolor($aNewImageWidth, $aNewImageHeight);
        imagecopyresampled($lNewImageDescriptor, $lInitialImageDescriptor, 0, 0, $lInitialImageCroppingX, $lInitialImageCroppingY, $aNewImageWidth, $aNewImageHeight, $lCroppedImageWidth, $lCroppedImageHeight);
        $func = 'image' . $lImageExtension;

        // сохраняем полученное изображение в указанный файл
        return $func($lNewImageDescriptor, $aNewImageFilePath);
    }

    public function getTop5TradingAccaunt()
    {
        $query = new Query();
        $menejers = $query
            ->Select(['ta.position', 'ta.id', 'ta.six_month_profit', 'ta.date_add', 'ta.one_month_profit', 'ta.one_week_profit', 'ta.profit', 'ta.one_year_profit', 'ta.name', 'u.username'])
            ->from(['trading_account ta'])
            ->leftJoin('user u', 'ta.user_id = u.id')
            ->where('ta.is_du = 1 AND ta.is_active = 1')
            ->orderBy('ta.position DESC')
            ->limit(5)
            ->all();

        foreach ($menejers as &$item) {
            $days = (int)((time() - strtotime($item['date_add'])) / (60 * 60 * 24));
            $item['days'] = (!empty($days)) ? $days : 1;
            $item['persent_day'] = $item['one_month_profit'] / 31;
            $item['mount_profit'] = 0;

            $ids_invest = Yii::$app->db->createCommand('SELECT id FROM investments WHERE trading_account_id = ' . $item['id'])->queryAll();
            $summ = 0;
            foreach ($ids_invest as $id_invest) {
                $investments_daily = InvestmentDailyLog::getLogsMount($id_invest['id']);
                $count = count($investments_daily);

                for ($i = 0; $i < $count; $i++) {
                    $summ += $investments_daily[$i]->summ_start * ($investments_daily[$i]->profit / 100);
                }
            }
            $item['mount_profit'] += $summ;
        }
        Options::setOptionValueByKey('top_trade_accaunt', serialize($menejers));
    }


    public function getThatSameTrader($count)
    {
        if ($count == 1) {
            return 'Тот самый </br> один трейдер';
        }
        $firstLine = 'Те самые </br>';
        $numberWord = $this->num_propis($count);

        $lastNumber = $count % 10;
        $lastWord = '';
        switch ($lastNumber) {
            case 1:
                $lastWord = " трейдер";
                break;
            case 3:
            case 4:
            case 2:
                $lastWord = " трейдера";
                break;
            default:
                $lastWord = " трейдеров";
        }

        return $firstLine . $numberWord . $lastWord;
    }

    public function getPaginator($pages)
    {
        $count = $pages->totalCount / $pages->pageSize;
        $out = '';

        if (is_float($count)) {
            $count = (int)($count + 1);
        }

        if ($count > 1) {
            $out .= '<ul class="pagination">';
            $url = '/' . $_SERVER['REDIRECT_URL'];
            $active_page = 0;
            $first = true;
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    if ($key == 'page') {
                        $active_page = $value;
                        continue;
                    }
                    if ($key == '_pjax') {
                        continue;
                    }
                    if ($key == 'id') {
                        continue;
                    }

                    if ($first) {
                        $first = false;
                        $url .= '?' . $key . '=' . $value;
                    } else {
                        $url .= '&' . $key . '=' . $value;
                    }
                }
            }
            if ($first) {
                $first = false;
                $url .= '?';
            }
            $i = (isset($_GET['page'])) ? $_GET['page'] - 1 : 0;
            $last_page = $count - 1;

            if ($i > 5) {
                $out .= '<li><a href="' . Url::to([$url . '&page=1']) . '" data-page="0">1</a></li>';
                $out .= '<li><a href="#" onclick="return false" data-page="0">...</a></li>';
            }

            for ($j = $i - 5; $j < ($i + 5); $j++) {
                if ($j < 0)
                    $j = 0;

                if ($j >= $count)
                    continue;

                $class = '';
                if ($j + 1 == $active_page) {
                    $class = ' class="active"';
                }
                $out .= '<li' . $class . '><a href="' . Url::to([$url . '&page=' . ($j + 1)]) . '" data-page="' . $j . '">' . ($j + 1) . '</a></li>';
            }
            if ($j < $count) {
                $out .= '<li><a href="#" onclick="return false" data-page="' . ($count - 1) . '">...</a></li>';
                $out .= '<li' . $class . '><a href="' . Url::to([$url . '&page=' . ($count)]) . '" data-page="' . ($count - 1) . '">' . $count . '</a></li>';

            }
            $out .= '</ul>';
        }

        return $out;
    }

    function num_propis($num)
    { // $num - цело число

        # Все варианты написания чисел прописью от 0 до 999 скомпонуем в один небольшой массив
        $m = array(
            array('ноль'),
            array('-', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'),
            array('-', '-', 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'),
            array('-', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'),
            array('-', 'одна', 'две')
        );

        # Все варианты написания разрядов прописью скомпануем в один небольшой массив
        $r = array(
            array('...ллион', '', 'а', 'ов'), // используется для всех неизвестно больших разрядов
            array('тысяч', 'а', 'и', ''),
            array('миллион', '', 'а', 'ов'),
            array('миллиард', '', 'а', 'ов'),
            array('триллион', '', 'а', 'ов'),
            array('квадриллион', '', 'а', 'ов'),
            array('квинтиллион', '', 'а', 'ов')
            // ,array(... список можно продолжить
        );

        if ($num == 0) return $m[0][0]; # Если число ноль, сразу сообщить об этом и выйти
        $o = array(); # Сюда записываем все получаемые результаты преобразования

        # Разложим исходное число на несколько трехзначных чисел и каждое полученное такое число обработаем отдельно
        foreach (array_reverse(str_split(str_pad($num, ceil(strlen($num) / 3) * 3, '0', STR_PAD_LEFT), 3)) as $k => $p) {
            $o[$k] = array();

            # Алгоритм, преобразующий трехзначное число в строку прописью
            foreach ($n = str_split($p) as $kk => $pp)
                if (!$pp) continue; else
                    switch ($kk) {
                        case 0:
                            $o[$k][] = $m[4][$pp];
                            break;
                        case 1:
                            if ($pp == 1) {
                                $o[$k][] = $m[2][$n[2]];
                                break 2;
                            } else$o[$k][] = $m[3][$pp];
                            break;
                        case 2:
                            if (($k == 1) && ($pp <= 2)) $o[$k][] = $m[5][$pp]; else$o[$k][] = $m[1][$pp];
                            break;
                    }
            $p *= 1;
            if (!$r[$k]) $r[$k] = reset($r);

            # Алгоритм, добавляющий разряд, учитывающий окончание руского языка
            if ($p && $k) switch (true) {
                case preg_match("/^[1]$|^\\d*[0,2-9][1]$/", $p):
                    $o[$k][] = $r[$k][0] . $r[$k][1];
                    break;
                case preg_match("/^[2-4]$|\\d*[0,2-9][2-4]$/", $p):
                    $o[$k][] = $r[$k][0] . $r[$k][2];
                    break;
                default:
                    $o[$k][] = $r[$k][0] . $r[$k][3];
                    break;
            }
            $o[$k] = implode(' ', $o[$k]);
        }

        return implode(' ', array_reverse($o));
    }

    public function is_open($arr, $key = 'url')
    {

        foreach ($arr as $item) {
            if ($item['url'][0] == '/' . $key)
                return true;
            else if ($key == 'user/bonds' && $item['url'][0] == '/user/strategy')
                return true;
        }
        return false;
    }


    public function getTop5AccountsIds()
    {

        $return = [
            'id' => [],
            'position' => [],
        ];
        $return['class'] = ['platinum ', 'gold ', 'silver ', 'bronze ', 'black '];
        $return['word'] = ['Platinum', 'Gold', 'Silver', 'Bronze', 'Black'];
        if (!$array = TradingAccount::find()->where(['is_du' => 1, 'is_active' => 1, 'show' => 1])->orderBy('position DESC')->limit(5)->all()) {
            return $return;
        };
        $position = 0;
        foreach ($array as $a) {
            $return['id'][] = $a->id;
            $return['position'][] = ++$position;
        }
        return $return;
    }

    public function export($data, $title, $filename = 'data_export.csv')
    {
        ob_start();
        $df = fopen("php://output", 'w');
        fputcsv($df, $title, ';');
        foreach ($data as $row) {
            fputcsv($df, $row, ';');
        }
        fclose($df);
        $res = ob_get_clean();

        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename=" . $filename);
        header("Content-Transfer-Encoding: binary");

        echo $res;
        die;
    }

    public function mergeHystory()
    {
        $result1 = [];
        $result2 = [];
        Yii::$app->db->createCommand('delete from trading_account_history_terminal_2')->execute();

        Yii::$app->db->createCommand('
    			    INSERT INTO trading_account_history_terminal_2 ( id, id_terminal, id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP )
					SELECT id, id_terminal, id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP 
					FROM trading_account_history_terminal
			')
            ->execute();

        $dublicates2 = Yii::$app->db->createCommand('SELECT id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP, COUNT(*) AS duplicates
                        FROM trading_account_history_terminal_2
                        GROUP BY id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP
                        HAVING duplicates > 1')
            ->queryAll();

        if (!empty($dublicates2)) {
            Yii::$app->db->createCommand('delete from trading_account_history_terminal_2')->execute();

            Yii::$app->db->createCommand('
    			    INSERT INTO trading_account_history_terminal_2 ( id, id_terminal, id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP )
					SELECT id, id_terminal, id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP 
					FROM trading_account_history_terminal
			')
                ->execute();

            $dublicates2 = Yii::$app->db->createCommand('SELECT id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP, COUNT(*) AS duplicates
                        FROM trading_account_history_terminal_2
                        GROUP BY id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP
                        HAVING duplicates > 1')
                ->queryAll();
        }

        $dublicates1 = Yii::$app->db->createCommand('SELECT id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP, COUNT(*) AS duplicates
                        FROM trading_account_history_terminal
                        GROUP BY id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP
                        HAVING duplicates > 1')
            ->queryAll();

        if (!empty($dublicates1)) {
            foreach ($dublicates1 as $d) {
                $r['trading_account_id'] = $d['id_trading'];
                $r['close_date'] = $d['CLOSE_DATE'];
                $result1[] = $r;
            }
            Options::setOptionValueByKey('terminal_history_duplicate_1', serialize($result1));
        } else {
            Options::setOptionValueByKey('terminal_history_duplicate_1', false);
        }
        if (!empty($dublicates2)) {
            foreach ($dublicates2 as $d) {
                $r['trading_account_id'] = $d['id_trading'];
                $r['close_date'] = $d['CLOSE_DATE'];
                $result2[] = $r;
            }
            Options::setOptionValueByKey('terminal_history_duplicate_2', serialize($result2));
        } else {
            Options::setOptionValueByKey('terminal_history_duplicate_2', false);
        }
    }

    public function getDsnAttribute($name, $dsn)
    {
        if (preg_match('/' . $name . '=([^;]*)/', $dsn, $match)) {
            return $match[1];
        } else {
            return null;
        }
    }


    public function getWeekWord($number)
    {
        $a = $number % 10;
        if ($a == 1) {
            return 'неделя';
        }
        if ($a >= 2 AND $a < 5) {
            return 'недели';
        }
        return 'недель';
    }


    function datediff($interval, $datefrom, $dateto, $using_timestamps = false)
    {
        /*
        $interval can be:
        yyyy - Number of full years
        q    - Number of full quarters
        m    - Number of full months
        y    - Difference between day numbers
               (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
        d    - Number of full days
        w    - Number of full weekdays
        ww   - Number of full weeks
        h    - Number of full hours
        n    - Number of full minutes
        s    - Number of full seconds (default)
        */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }

        $difference = $dateto - $datefrom; // Difference in seconds
        $months_difference = 0;

        switch ($interval) {
            case 'yyyy': // Number of full years
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }

                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }

                $datediff = $years_difference;
                break;

            case "q": // Number of full quarters
                $quarters_difference = floor($difference / 8035200);

                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $quarters_difference--;
                $datediff = $quarters_difference;
                break;

            case "m": // Number of full months
                $months_difference = floor($difference / 2678400);

                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $months_difference--;

                $datediff = $months_difference;
                break;

            case 'y': // Difference between day numbers
                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;

            case "d": // Number of full days
                $datediff = floor($difference / 86400);
                break;

            case "w": // Number of full weekdays
                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?

                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }

                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }

                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;

            case "ww": // Number of full weeks
                $datediff = floor($difference / 604800);
                break;

            case "h": // Number of full hours
                $datediff = floor($difference / 3600);
                break;

            case "n": // Number of full minutes
                $datediff = floor($difference / 60);
                break;

            default: // Number of full seconds (default)
                $datediff = $difference;
                break;
        }

        return $datediff;
    }
}
