<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\models\User;
use common\models\AmoQueue;
class VisitorLog extends ActiveRecord
{

    const cities = [
        1 => 'Moscow',
        2 => 'Majuro',
        3 => 'London'
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'visitor_log';
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
        return [];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function makeRecord($name,$phone,$date_visit,$city_id)
    {
        if(!isset(static::cities[$city_id]) OR !$name OR $date_visit < date('Y-m-d', strtotime(' +1 day'))) {
            return false;
        }
        $log = new static();
        $log->name = $name;
        $log->phone = User::clearPhone($phone);
        $log->date_visit = $date_visit;
        $log->city_id = $city_id;

       if (!Yii::$app->user->isGuest) {
           AmoQueue::addTask( 'actionCreateMoscowLead',Yii::$app->user->id, serialize(2));
       }


        return $log->save();
    }

    public static function getNewCount() {
        return static::find()->where(['sms_confirmed' => 1, 'status'=>0])->count();
    }

}
