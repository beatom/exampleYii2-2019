<?php
namespace common\models;

use common\models\User;
use yii\db\ActiveRecord;
use Yii;


/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $chat_message_id
 * @property boolean $like
 * @property date $date_add
 */
class ChatMessageMark extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_message_mark';
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

    public function attributeLabels()
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


    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getMark($chat_message_id, $user_id)
    {
        $mark = static::find()->where(['user_id' => $user_id, 'chat_message_id' => $chat_message_id])->orderBy('date_add DESC')->one();
        return $mark ? $mark : false;
    }

    public static function mark($chat_message_id, $user_id, $like = true)
    {

        if(!$chat_message = ChatMessage::findIdentity($chat_message_id)) {
            return false;
        }

        if($mark = static::getMark($chat_message_id, $user_id)) {
            if($mark->like == $like) {
                return false;
            }
            if($like) {
                $chat_message->dislikes -= 1;
                $chat_message->likes += 1;
            } else {
                $chat_message->dislikes += 1;
                $chat_message->likes -= 1;
            }
        } else {
            if($like) {
                $chat_message->likes += 1;
            } else {
                $chat_message->dislikes += 1;
            }
        }

        $new_mark = new  static();
        $new_mark->like = $like;
        $new_mark->chat_message_id = $chat_message_id;
        $new_mark->user_id = $user_id;
        $new_mark->save();

        $chat_message->likes = $chat_message->likes > 0 ? $chat_message->likes : 0;
        $chat_message->dislikes = $chat_message->dislikes > 0 ? $chat_message->dislikes : 0;

        $chat_message->save();
        return true;
    }


    public static function markByUser($chat_message, $user_id) {
        if(($chat_message->likes > 0 AND $chat_message->dislikes > 0) OR !$mark = static::getMark($chat_message->id, $user_id)) {
            return $chat_message;
        }

        if($mark->like AND $chat_message->likes == 0) {
            $chat_message->likes++;
        } elseif(!$mark->like AND $chat_message->dislikes == 0) {
            $chat_message->dislikes++;
        }
        return $chat_message;
    }

}
