<?php
namespace backend\models;

use common\models\Sender;
use common\models\UserMessage;
use yii\base\Model;
use common\models\User;
use common\models\ChatMessage;
use common\models\Chat;
use Yii;
use yii\db\ActiveRecord;


/**
 * MessageMassForm model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $sender_id
 * @property date $date_add
 * @property string $text
 * @property string $title
 */
class MessageMassForm extends ActiveRecord
{
//    public $sender_id;
//    public $user_id;
//    public $text;

    public static function tableName()
    {
        return 'chat_mass_messages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['sender_id', 'integer' ],
            ['sender_id', 'required', 'message'=>'Выберите отправителя'],
            [['text', 'title'], 'trim'],
            [['text', 'title'], 'required', 'message'=>'Необходимо заполнить'],
            [['text', 'title'], 'string'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getSender()
    {
        return $this->hasOne(Sender::class, ['id' =>'sender_id']);
    }

    /**
     * Signs user up.
     
     */
    public function sendMessageToEveryone()
    {

        if ($this->save()) {
            Yii::$app->session->setFlash('success', 'Сообщение было успешно отправлено');

            foreach (User::getUsersList() as $recipient) {
              //  $chatId = Chat::makeChat($recipient->id, $this->sender_id);
                UserMessage::sendMessage($recipient->id, $this->sender_id, $this->text,  $this->title);
            }
            return true;

            $this->save();
        } else {
            return false;
        }
    }
}
