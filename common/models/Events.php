<?php
namespace common\models;


use yii\db\ActiveRecord;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property double $bank_percent
 * @property double $coefficient
 * @property string $bookmaker
 * @property string $title
 * @property string $bet
 * @property integer $result
 * @property boolean $free
 * @property integer $responsible_user_id
 * @property integer $days_log_id
 * @property boolean $boolean
 * @property boolean $show
 * @property date $updated_at
 */
class Events extends ActiveRecord
{

    static $results = [
        0 => 'Не выбрано',
        1 => 'Выигрыш',
        2 => 'Проигрыш',
        3 => 'Возврат (0)',
    ];

    static $results_colors = [
        1 => 'green',
        2 => 'red',
        3 => '',
    ];

    static $results_classes = [
        0 => '',
        1 => 'up',
        2 => 'down',
        3 => 'equal',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'events';
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
            [['title', 'date_add'], 'required'],
            [['bank_percent', 'coefficient', 'bookmaker', 'bet', 'result', 'updated_at'], 'safe'],
            [['title', 'bookmaker', 'bet'], 'string'],
            ['result', 'in', 'range' => [0, 1, 2, 3]],
            ['coefficient', 'double', 'min' => 0,],
            [['coefficient', 'bank_percent'], 'default', 'value' => 0],
            ['bank_percent', 'double', 'min' => 0, 'max' => 100, 'message' => 'Процент должен быть от 0 до 100'],
            ['date_add', 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['responsible_user_id', 'days_log_id'], 'safe'],
            [['free', 'show'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'bank_percent' => 'Процент от банка',
            'date_add' => 'Дата и время',
            'coefficient' => 'Коэффициент ',
            'bookmaker' => 'Букмекер',
            'title' => 'Событие',
            'bet' => 'Ставка',
            'result' => 'Результат',
            'free' => 'Замок',
            'responsible_user_id' => 'Ответственный пользователь',
            'show' => 'Отображаеться пользователям',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getDays_log()
    {
        return $this->hasOne(DaysLog::class, ['id' => 'days_log_id']);
    }


    public static function getEvents($days_log_id = false, $as_array = false, $sort = 'DESC')
    {
        $query = static::find()->orderBy('date_add ' . $sort);
        if ($days_log_id) {
            $query->where(['days_log_id' => $days_log_id, 'show' => true]);
        }
        if ($as_array) {
            $query->asArray();
        }
        return $query->all();
    }

    public function saveEvent()
    {
        if (!$this->validate()) {
            return false;
        }
        $days_log = $this->days_log;
        if (date('H') < 10) {
            $start_date = date('Y-m-d 15:00', strtotime(' -1 day'));
        } else {
            $start_date = date('Y-m-d 15:00');
        }
        $end_date = date('Y-m-d 09:59:59', strtotime($days_log->date_add . ' +1 day'));
        if ($this->date_add > $end_date OR $this->date_add < $start_date) {
            $this->addError('date_add', "Дата должна быть между $start_date и $end_date");
            return false;
        }

        if ($this->bank_percent) {
            $day_bank_sum = static::find()->where(['days_log_id' => $this->days_log_id]);
            if ($this->id) {
                $day_bank_sum->andWhere('id <> ' . $this->id);
            }
            $day_bank_sum = $day_bank_sum->sum('bank_percent');
            if (($day_bank_sum + $this->bank_percent) > 100) {

                $this->addError('bank_percent', 'Сумма процента от банка за день не может превышать 100%. Доступный процент = ' . (100 - floatval($day_bank_sum)));
                return false;
            }
        }

        //if(array_intersect(['',null,false,0], [$this->bank_percent, $this->bookmaker, $this->coefficient, $this->bet])) {
        $this->show = true;
        // }

        $this->responsible_user_id = \Yii::$app->user->id;
        $this->updated_at = date('Y-m-d H:i:s');
        if ($this->save() AND $this->show) {
            DaysLog::countDay($this->date_add);
            User::setEventsNotice();
            
            Yii::$app->cache->delete('statistic_home');
            Yii::$app->cache->delete('statistic_cabinet');
            Yii::$app->cache->getOrSet('statistic_home', function () {
                return DaysLog::getTable(true);
            });
            Yii::$app->cache->getOrSet('statistic_cabinet', function () {
                return DaysLog::getTable();
            });
            
            return true;
        }
        return false;
    }

    public static function getCurrentBankPercent()
    {
        $time = date('H');
        if ($time >= 10 AND $time <= 14) {
            return 0;
        }

        $query_date = date('Y-m-d');
        if ($time < 10) {
            $query_date = date('Y-m-d', strtotime(' -1 day'));
        }
        $days_log = DaysLog::getLog($query_date, 'events_complete');
        $bank_percent_sum = 0;
        foreach ($days_log->events_complete as $event) {
            $bank_percent_sum += $event->bank_percent;
        }
        return $bank_percent_sum;
    }

}
