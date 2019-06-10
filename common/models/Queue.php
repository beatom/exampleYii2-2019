<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property date $date_work
 * @property integer $worked
 * @property string $task
 * @property integer $type
 * @property text $params
 *
 */
class Queue extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'queue_test';
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

    /**
     * @inheritdoc
     */
    public static function findByNotWorket()
    {
        return static::find()
            ->where('worked = 0')
            ->orderBy('date_add')
            ->all();
    }

    public static function setWorked($ids){
        $ids = implode(',', $ids);
        return Yii::$app->db->createCommand('UPDATE `queue_test` set `worked` = 1, `date_work` = "'.date("Y-m-d H:i:s").'" WHERE `id` IN ('.$ids.')')->execute();
    }

    public static function addTask( $aktion, $type=0, $params = '' ){
        $queue = new Queue();
        $queue->task = $aktion;
        $queue->type = $type;
        $queue->params = $params;
        $queue->date_add = date('Y-m-d H:i:s');
        $queue->save();
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


}
