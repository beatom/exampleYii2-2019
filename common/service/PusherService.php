<?php

namespace common\service;


use common\models\ChatMessage;
use Yii;
use common\models\User;

class PusherService
{
    private static $instance = null;
    
    const response = ['subscribeKey' => 'eventMonitoring'];
    const reboot = ['subscribeKey' => 'rebootServer'];

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    public function sendMessage(ChatMessage $message, $new_message = false)
    {
        $service = Servis::getInstance();
        $data = [
            'type' => $new_message ? 'add' : 'update',
            'id' => $message->id,
            'text' => nl2br($message->text),
            'date_add' => $message->date_add,
            'parent_id' => $message->parent_id,
            'likes' => $message->likes,
            'dislikes' => $message->dislikes,
            'branch_id' => $message->branch_id,
            'parent_name' => ''
        ];
        if($message->parent_id AND $parent = ChatMessage::find()->where($message->parent_id)->with('user')->one()) {
            $data['parent_name'] = $parent->user->firstname ? $parent->user->firstname : $parent->user->username;
        }
        $user = $message->user;
        $data_user = [
            'id' => $user->id,
            'name' => $user->firstname ? $user->firstname : $user->username,
            'name_string' => $user->getNameString(),
            'avatar' => $user->avatar,
            'balance' => $service->beautyDecimal($user->getBalance(), 0),
            'verified' => $user->verified,
            'messages_count' => ChatMessage::find()->where(['user_id'=>$user->id, 'deleted_at' => null])->count(),
            'social' => $user->social->getArray(),
        ];
        $user->chat_messages_count = $data_user['messages_count'];
        $user->save();
        $data['user'] = $data_user;
        $this->send($data);
    }

    public function deleteMessage($message_id)
    {
        if(!is_array($message_id)) {
            $message_id = [$message_id];
        }

        $data = [
            'type' => 'delete',
            'id' => $message_id,
        ];
        $this->send($data);
    }

    public function updateUsersMessagesCount($id) {
        if(!is_array($id)) {
            $id = [$id];
        }

        $data = [
            'type' => 'messages_count',
            'users' => []
        ];
        foreach (User::find()
                     ->select(['*','total_b'])
                     ->where(['id' => $id])
                     ->leftJoin('(SELECT COUNT(*) as total_b, user_id FROM `chat_message` WHERE deleted_at is NULL GROUP BY user_id) as cm ON cm.user_id = user.id')
                     ->all() as $user) {
            $user->chat_messages_count = $user->total_b ? $user->total_b : 0;
            $user->save();
            $new_row = [
                'id' => $user->id,
                'messages_count' => $user->chat_messages_count
            ];
            $data['users'][] = $new_row;
        }
        $this->send($data);
    }


    public function send($data = []) {
        $message = static::response;
        $message['data'] = $data;
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'desktop');
        if($socket instanceof \ZMQSocket)
        {
            // Здесь тоже передаём идентификатор, чтобы в push классе мы смогли получить объект topic
            $eventData = json_encode($message);
            $socket->connect("tcp://127.0.0.1:5555");
            $socket->send($eventData);
        }
    }

    public function drop() {
        $message = static::reboot;
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'desktop');
        if($socket instanceof \ZMQSocket)
        {
            // Здесь тоже передаём идентификатор, чтобы в push классе мы смогли получить объект topic
            $eventData = json_encode($message);
            $socket->connect("tcp://127.0.0.1:5555");
            $socket->send($eventData);
        }
    }


}
