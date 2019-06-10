<?php
namespace common\models;

use common\service\Servis;
use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property integer $user_id
 * @property string $action
 * @property string $data
 * @property string $answer
 *
 */
class ApiErrorLog extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_error_log';
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
    public function getId()
    {
        return $this->getPrimaryKey();
    }


    public static function addLog($action, $data, $answer, $user_id=null){
        $newLog = new static();
        $newLog->action = $action;
        $newLog->data = json_encode($data);
        $newLog->answer = json_encode($answer);
        $newLog->user_id = $user_id;
        return $newLog->save();
    }


}
