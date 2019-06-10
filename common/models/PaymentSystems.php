<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property string $title
 * @property string $system
 * @property string $via
 * @property integer $sum_min
 * @property integer $sum_max
 * @property string $image
 * @property integer $currency_id
 * @property double $fee
 * @property double $fee_add
 * @property boolean $show
 * @property string $comment
 * @property integer $position
 * @property double $fee_verified
 */
class PaymentSystems extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_systems';
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
            [['system','via','image','currency_id'], 'safe'],
            [['title','sum_min','sum_max','fee','fee_add', 'show', 'position', 'fee_verified'], 'required'],
            ['title', 'string'],
            [['sum_max','sum_min'], 'integer', 'min' => 5],
            [['position'], 'integer', 'min' => 0],
            [['fee','fee_add', 'fee_verified'], 'double', 'min' => 0],
            ['show', 'boolean']
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => 'Подпись',
            'sum_max' => 'Максимальная сумма пополнения ',
            'sum_min' => 'Минимальная сумма пополнения',
            'fee_add' => 'Коммисия добавляемая реально',
            'fee' => 'Коммисия отображаемая',
            'fee_verified' => 'Для верифицированных'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }


    public function getCurrency()
    {
        return $this->hasOne(Currencies::class, ['id' => 'currency_id']);
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
        $query = static::find()->with('currency')->orderBy('position ASC');
        if ($show) {
            $query->where('`show` = true');
        }
        if ($array) {
            $query->asArray();
        }
        return $query->all();
    }

}
