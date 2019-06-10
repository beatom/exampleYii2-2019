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
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property string $template
 * @property text $message
 *
 */
class QueueMail extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'queue_mail';
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
            ->limit(3)
            ->all();
    }

    public static function setWorked($ids){
        $ids = implode(',', $ids);
        return Yii::$app->db->createCommand('UPDATE `queue_mail` set `worked` = 1, `date_work` = "'.date("Y-m-d H:i:s").'" WHERE `id` IN ('.$ids.')')->execute();
    }

    public static function addTask( $from, $to, $subject, $template, $message ){
        $queue = new QueueMail();
        $queue->date_add = date('Y-m-d H:i:s');
        $queue->from = $from;
        $queue->to = $to;
        $queue->subject = $subject;
        $queue->template = $template;
        $queue->message = serialize($message);
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
