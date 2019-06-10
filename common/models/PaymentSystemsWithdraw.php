<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property string $title
 * @property string $image
 * @property integer $currency_id
 * @property double $fee
 * @property boolean $show
 * @property integer $position
 * @property double $fee_verified
 * @property double $sum_min
 * @property double $sum_max
 */
class PaymentSystemsWithdraw extends ActiveRecord
{

    public $currency_rate = 1;
    public $currency_name = '';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_systems_withdraw';
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
            [['image','currency_id'], 'safe'],
            [['title','fee','show', 'sum_min','sum_max', 'position', 'fee_verified'], 'required'],
            ['title', 'string'],
            [['position'], 'integer', 'min' => 0],
            [['fee', 'fee_verified'], 'double', 'min' => 0],
            ['show', 'boolean'],
            [['sum_max','sum_min'], 'integer', 'min' => 5],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Подпись',
            'fee' => 'Коммисия',
            'fee_verified' => 'Коммисия для верифицированных',
            'sum_max' => 'Максимальная сумма вывода ',
            'sum_min' => 'Минимальная сумма вывода',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findActive($id)
    {
        return static::findOne(['id' => $id, 'show' => 1]);
    }


    public function currency()
    {
        if(!$this->currency_id OR !$currency = Currencies::findIdentity($this->currency_id)) {
            $currency = new Currencies();
            $currency->synonym = 'USD';
            $currency->value = 1;
        } else {
            $currency->synonym = 'RUB';
        }
        return $currency;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getSystems($show = true, $array = false)
    {
        $query = static::find()->orderBy('position ASC');
        if ($show) {
            $query->where('`show` = true');
        }
        if ($array) {
            $query->asArray();
        }
        $result = $query->all();
        $currencies = Currencies::find()->all();
        $c = [];
        foreach ($currencies as $currency) {
            $c[$currency->id] = $currency->value;
        }
        if(!$array) {
            foreach ($result as $key => $r) {
                if($r->currency_id AND isset($c[$r->currency_id])) {
                    $result[$key]->currency_rate =  $c[$r->currency_id];
                    $result[$key]->currency_name = 'RUB';
                } else {
                    $result[$key]->currency_name = 'USD';
                }
            }
        } else {
            foreach ($result as $key => $r) {
                if($r['currency_id'] AND isset($c[$r['currency_id']])) {
                    $result[$key]['currency_rate'] =  $c[$r['currency_id']];
                    $result[$key]['currency_name'] =  'RUB';
                } else {
                    $result[$key]['currency_name'] = 'USD';
                    $result[$key]['currency_rate'] = 1;
                }
            }
        }
        return $result;
    }

}
