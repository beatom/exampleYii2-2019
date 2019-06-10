<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;
use common\models\QueueMail;
use common\service\CBRAgent;
/**
 * User model
 *
 * @property integer $id
 * @property string $synonym
 * @property double $value
 * @property string $name
 * @property date $updated_at
 * @property boolean $status
 */
class Currencies extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currencies';
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

    public static function getRate($key)
    {
        $cur = static::find()->where(['synonym' => $key])->one();
        return $cur ? $cur->value : false;
    }

    public static function updateRates()
    {
        $rates = new CBRAgent();
        if(!$rates->load()) {
            return false;
        }
        foreach (static::find()->where(['status' => true])->all() as $cur) {
            if($summ = $rates->get($cur->synonym)) {
                $cur->value = number_format($summ, 2);
                $cur->updated_at = date('Y-m-d H:i:s');
                $cur->save();
            }
        }
        return true;
    }


}
