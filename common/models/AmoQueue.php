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
 * @property boolean $worked
 * @property string $task
 * @property text $params
 * @property text $additional_params
 */
class AmoQueue extends ActiveRecord
{
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amo_queue';
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
          //  ->andWhere('task <> "actionUpdateUsers" OR id = 14801')
            ->limit(500)
            ->all();
    }

    public static function setWorked($ids){
        if(empty($ids) OR !$ids) {
            return false;
        }
        $ids = implode(',', $ids);
        return Yii::$app->db->createCommand('UPDATE `amo_queue` set `worked` = 1, `date_work` = "'.date("Y-m-d H:i:s").'" WHERE `id` IN ('.$ids.')')->execute();
    }

    public static function addTask( $task, $params = '', $additional_params = ''){
        if($task == 'actionChangeUserLead') {
            static::updateAll(['worked' => 0], ['task' => ['actionChangeUserLead', 'actionAddUserLead'], 'params' => $params, 'worked' => 0]);
        }
        $queue = new static();
        $queue->task = $task;
        $queue->params = $params;
        $queue->additional_params = $additional_params;
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
