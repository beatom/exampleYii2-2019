<?php
namespace common\models;

use common\models\Chat;
use common\service\PusherService;
use DateTime;
use Yii;
use yii\db\ActiveRecord;

if (\Yii::$app->language == 'ru') {
    setlocale(LC_ALL, 'ru_RU.UTF-8');
}

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $parent_id
 * @property integer $branch_id
 * @property datetime $date_add
 * @property integer $responsible_id
 * @property string $text
 * @property integer $dislikes
 * @property integer $likes
 * @property date $deleted_at
 */
class ChatMessage extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_message';
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
            // username and password are both required
            // [['text'], 'required'],
            [['text', 'dislikes', 'likes', 'user_id'], 'required'],
            [['responsible_id', 'parent_id', 'deleted_at', 'date_add', 'branch_id'], 'safe'],

            [['text'], 'string', 'length' => [1, 650000]],
            [['dislikes', 'likes'], 'integer', 'min' => 0, 'tooSmall' => 'Значение не может быть меньше 0'],
            [[ 'dislikes' , 'likes' ], 'default' , 'value' => 0 ],
            ['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id', 'message' => 'Пользователя с таким id не существует'],
            ['parent_id', 'exist', 'targetClass' => static::class, 'targetAttribute' => 'id', 'message' => 'Сообщения с таким id не существует', 'when' => function ($model) {
                return !empty($model->parent_id);
            }],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Сообщение',
            'user_id' => 'id Пользователя',
            'parent_id' => 'id Старшего сообщения',
            'date_add' => 'Дата добавления',
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if(!key_exists('deleted_at', $changedAttributes)) {
            $pusher_service = PusherService::getInstance();
            $pusher_service->sendMessage($this, $insert);
        }
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
        return $this->hasOne(User::class, ['id' => 'user_id'])->with('social');
    }

    public function getChilds()
    {
        return $this->hasMany(ChatMessage::class, ['branch_id' => 'id'])->where(['deleted_at' => null])->with(['user', 'parent']);
    }

    public function getMark()
    {
        return $this->hasOne(ChatMessageMark::class, ['id' => 'chat_message_id', 'user_id' => Yii::$app->user->id])->orderBy('id DESC');
    }

    public function getParent()
    {
        return $this->hasOne(ChatMessage::class, ['id' => 'parent_id'])->where(['deleted_at' => null])->with(['user']);
    }

    public static function sendMessage($user_id, $text, $parent_id = null)
    {
        if (static::find()->where(['user_id' => $user_id])->andWhere('date_add >= "' . date('Y-m-d H:i:s', strtotime('-1 seconds')) . '"')->exists() OR UserBan::getUserStatus($user_id)) {
            var_dump(static::find()->where(['user_id' => $user_id])->andWhere('date_add >= "' . date('Y-m-d H:i:s', strtotime('-5 seconds')) . '"')->exists());
            var_dump(UserBan::getUserStatus($user_id));
            //TODO блокировка пользователя за спам
            return false;
        }
        $message = new ChatMessage();
        $message->parent_id = $parent_id;
        $message->user_id = $user_id;
        $message->text = htmlentities($text);
        $message->likes = 0;
        $message->dislikes = 0;
        $message->date_add = date('Y-m-d H:i:s');
        $message->branch_id = null;
        if($message->parent_id) {
            $parent = static::findIdentity($message->parent_id);
            $message->branch_id = $parent->branch_id ? $parent->branch_id : $parent->id;
        }


        if($message->save()) {
            return true;
        } else {
            var_dump($message->errors);
        }
        return false;
    }

    public static function changeMessage($message_id, $user_id, $text, $likes = null, $dislikes = null)
    {
        if ($message = static::findIdentity($message_id)) {
            return false;
        }
        $message->responsible_id = $user_id;
        $message->text = htmlentities($text);
        $message->likes = $likes OR $likes == 0 ? $likes : $message->likes;
        $message->dislikes = $dislikes OR $dislikes == 0 ? $dislikes : $message->dislikes;
        return $message->save();
    }
    
    public function saveMessage() {
        $this->text = htmlentities($this->text);
        $this->date_add = $this->date_add ? $this->date_add : date('Y-m-d H:i:s');
        if($this->parent_id) {
            $parent = static::findIdentity($this->parent_id);
            $this->branch_id = $parent->branch_id ? $parent->branch_id : $parent->id;
        }
        
        if(!$this->validate()) {
            var_dump($this->errors);
            die;
            return false;
        }
        
        return $this->save();
    }
    
    

    public static function deleteMessage($message_id, $user_id)
    {

        if (!$message = static::findIdentity($message_id)) {
            return false;
        }
        $message->responsible_id = $user_id;
        $message->deleted_at = date('Y-m-d H:i:s');
        $message->save();

        $data = [
            'users' => [$message->user_id],
            'messages' => [$message->id]
        ];
        $new_data = static::deleteMessagesArray($message_id, $user_id);
        $data['users'] = array_merge($data['users'], $new_data['users']);
        $data['messages'] = array_merge($data['messages'], $new_data['messages']);

        $pusher_service = PusherService::getInstance();
        $pusher_service->updateUsersMessagesCount($data['users']);
        $pusher_service->deleteMessage($data['messages']);
        
    }

    public static function deleteMessagesArray($message_id, $user_id) {
        $del_date = date('Y-m-d H:i:s');
        $data = [
            'users' => [],
            'messages' => []
        ];
        foreach ( static::find()->where(['parent_id' => $message_id])->with(['user', 'childs'])->all() as $del_message) {
            $del_message->deleted_at = $del_date;
            $del_message->responsible_id = $user_id;
            $del_message->save();
            $data['messages'][] = $del_message->id;
            if(!in_array($del_message->user_id, $data['users'])) {
                $data['users'][] = $del_message->user_id;
            }
            $new_data = static::deleteMessagesArray($del_message->id, $user_id);
            $data['users'] = array_merge($data['users'], $new_data['users']);
            $data['messages'] = array_merge($data['messages'], $new_data['messages']);
        }
        return $data;
    }


}
