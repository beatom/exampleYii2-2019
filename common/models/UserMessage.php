<?php
namespace common\models;

use common\models\Chat;
use common\models\User;
use DateInterval;
use DateTime;
use Yii;
use yii\db\ActiveRecord;
use common\models\QueueMail;
use yii\helpers\ArrayHelper;
use common\models\Sender;

if(\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $sender_id
 * @property date $date_add
 * @property string $text
 * @property boolean $status
 * @property date $date_delete
 * @property string $title
 */
class UserMessage extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_message';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_INSERT => ['date_add'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            // [['text'], 'required'],
            [['text', 'user_id', 'sender_id', 'title'], 'safe'],
            [['text'], 'string', 'length' => [1, 650000]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getSender()
    {
        return $this->hasOne(Sender::class, ['id' => 'sender_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function sendMessage($user_id, $sender_id, $text, $title)
    {
        $message = new static();
        $message->title = $title;
        $message->sender_id = $sender_id;
        $message->user_id = $user_id;
        $message->text = $text;
        $message->date_add = date('Y-m-d H:i:s');
        $message->save();

//        $companion = User::updateAllCounters(['unread_messages' => 1], ['id' => $user_id]);
       // $companion->updateCounters();


        $message->refresh();
        $data['time'] = strtotime($message->date_add);
        $data['message_id'] = $message->id;

        return $data;
    }

}
