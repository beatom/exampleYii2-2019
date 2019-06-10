<?php
namespace console\models;

use common\models\ChatMessage;
use common\models\ChatMessageMark;
use common\models\User;
use common\models\UserIpLog;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;
use Ratchet\Wamp\Topic;
use yii\helpers\Html;
use Ratchet\WebSocket\WsServerInterface;

class EventPusher implements WampServerInterface, WsServerInterface
{
    protected $subscribedTopics = array();
       protected $clients;
    protected $users;
    protected $user_sessions;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage; // Для хранения технической информации об присоединившихся клиентах используется технология SplObjectStorage, встроенная в PHP
    }

    public function onOpen(ConnectionInterface $conn)
    {
           $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        //echo "=================\n";
    }

    public function onSubscribe(ConnectionInterface $conn, $topic)
    {
        /*
            $topic->getId() сюда будет прилетать идентификатор, который вы будете предавать от клиента
            Мы будем это его сохранять и далее проверять наличие идентификатора.
            Это позволит посылать данные только подписавшимся клиентам.
        */
        $topic->add($conn);
        $subject = $topic->getId();
        if (!array_key_exists($subject, $this->subscribedTopics)) {
            $this->subscribedTopics[$subject] = $topic;
        }
        //  var_dump($this->clients);
    }

    public function onPushEventData($event)
    {

        $eventData = json_decode($event, true);
//        var_dump($eventData);
        if($eventData['subscribeKey'] == 'rebootServer') {
            echo "rebooting shutdown";
            die;
        }


//        $topic->add($conn);

        //Здесь в массиве $eventData мы тоже передаём идентификатор и проверяем есть ли подписанные клиенты.
        if (!array_key_exists($eventData['subscribeKey'], $this->subscribedTopics)) {
            var_dump(1);
            return;
        }

        // Через этот идентификатор получаем нужный нам объект instanceof Topic.
        $topic = $this->subscribedTopics[$eventData['subscribeKey']];

        if ($topic instanceof Topic) {
            foreach ($this->clients as $client) {
                if(!$topic->has($client)) {
                    $topic->add($client);
                }
            }
            $eventData = $this->prepareData($eventData);

            // Посылаем данные клиенту
            $topic->broadcast($eventData);
        } else {
            var_dump(2);
            return;
        }
    }

    /* Реализацию остальных методов не описываю */
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params)
    {
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }

    public function onPublish(ConnectionInterface $conn, $topic, $event, array
    $exclude, array $eligible)
    {
        return;
        var_dump([$event]);
        var_dump($topic->count());
        if (!isset($event['user_id']) OR !isset($event['session_id'])) {
           // $conn->close();
            echo "undefined user\n";
//            var_dump($event);
            return;
        }
        $user_id = $event['user_id'];
        $session_id = $event['session_id'];

        $topic_id = $topic->getId();
        if ($topic_id == 'user') {
            if (!isset($this->users[$session_id])) {
                if (!$user_id OR $user_id == '') {
                    $this->users[$session_id] = [
                        'user_id' => $user_id,
                        'conn_id' => [$conn->resourceId]
                    ];
                } else {
                    if (!isset($this->user_sessions[$user_id])) {
                        $this->user_sessions[$user_id] = [];
                    }

                    if (!isset($this->user_sessions[$user_id][$session_id]) OR $this->user_sessions[$user_id][$session_id] < date('Y-m-d H:i:s', strtotime('-2 days'))) {
                        $log = UserIpLog::find()->where(['user_id' => $user_id])->orderBy('id DESC')->one();
                        if ($session_id != $log->session_id AND UserIpLog::find()->where(['user_id' => $user_id, 'session_id' => $session_id])->andWhere('date_add >= "' . date('Y-m-d H:i:s', strtotime(' -30 days')) . '"')->exists()) {
                            $conn->close();
                            return;
                        }
                        $this->user_sessions[$user_id][$session_id] = date('Y-m-d H:i:s');
                    }
                    $this->users[$session_id] = [
                        'user_id' => $user_id,
                        'conn_id' => [$conn->resourceId]
                    ];
                }
            } elseif ($this->users[$session_id]['user_id'] != $user_id) {
                $conn->close();
//                foreach ($this->subscribedTopics as $key => $topic) {
//                    $topic->remove($conn);
//                }
            } else {
                $this->users[$session_id]['conn_id'][] = $conn->resourceId;
            }
            return;
        }
        if(!$this->checkUser($user_id, $session_id, $conn->resourceId)) {
//            var_dump('user not checked');
            $conn->close();
            return;
        }
        if(!isset($event['data'])) {
//            var_dump('data not setted');
            return;
        }
        $data = $event['data'];
        if($topic_id == 'message') {
            echo "message\n";
            if(!isset($data['message'])) {
                return;
            }
            $text = trim($data['message']);
            if(!$text OR $text == '') {
                return;
            }
            $parent_id = isset($data['parent_id']) ? $data['parent_id'] : null;
            
            if(!ChatMessage::sendMessage($user_id, $text, $parent_id)) {
                //$conn->close();
//                var_dump('message not send');
                return;
            }
        }

        if($topic_id == 'mark') {
            echo "mark\n";
            if(!isset($data['message_id']) OR !isset($data['like'])) {
//                var_dump($data);
                return;
            }
            $message_id = $data['message_id'];
            $mark = $data['like'] ? true : false;

            ChatMessageMark::mark($message_id, $user_id, $mark);
            return;
        }

        if($topic_id == 'change') {
            echo "change\n";
            $user = User::findIdentity($user_id);
            if(!$user OR !$user->can('moderator')) {
                return;
            }
            if(!isset($data['message']) OR !isset($data['message_id'])) {
                return;
            }
            $text = trim($data['message']);
            $message_id = $data['message_id'];
            if(!$text OR $text == '') {
                return;
            }
            $likes = isset($data['likes']) ? intval($data['likes']) : null;
            $dislikes = isset($data['dislikes']) ? intval($data['dislikes']) : null;
            ChatMessage::changeMessage($message_id, $user_id, $text, $likes, $dislikes);
            return;
        }

        if($topic_id == 'delete') {
            echo "delete\n";
            if(!isset($data['message_id'])) {
                return;
            }

            $message_id = $data['message_id'];
            $user = User::findIdentity($user_id);

            if(!$user OR !$user->can('moderator')) {
                return;
            }
            ChatMessage::deleteMessage($message_id, $user_id);
            return;
        }

            
//        var_dump($event);
    }

    private function checkUser($user_id, $session_id, $conn_id) {
        if (!$user_id OR $user_id == '') {
//            var_dump($user_id);
            return false;
        }
        if(!isset($this->users[$session_id])) {
//            var_dump($this->users);
            return false;
        }
        $session = $this->users[$session_id];
        if(!$user_id == $session['user_id'] OR !in_array($conn_id, $session['conn_id'])) {
//            var_dump(!$user_id == $session['user_id']);
//            var_dump(!in_array($conn_id, $session['conn_id']));
            return false;
        }
        return true;
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic)
    {
    }


    public function onClose(ConnectionInterface $conn)
    {
//        return false;
   $this->clients->detach($conn);

//        var_dump($this->users);
//        var_dump($this->user_sessions);

//        foreach ($this->users as $key => $session) {
//            if ($arr_key = array_search($conn->resourceId, $session['conn_id']) OR $arr_key == 0) {
//                unset($this->users[$key]['conn_id'][$arr_key]);
//            }
//        }

       // echo "({$conn->resourceId}) disconnected\n";

    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true); //для приема сообщений в формате json
//        var_dump([$data, $from]);
//        die;
//        if (is_null($data)) {
//            echo "invalid data\n";
//            return $from->close();
//        }
////        foreach ($this->clients as $client)
////        {
////            if($from !== $client)
////                $client->send($msg);
////        }
//        echo $from->resourceId . "\n";//id, присвоенное подключившемуся клиенту
    }

    public function getSubProtocols()
    {
        return ['ocpp1.6'];
    }


    private function prepareData($eventData)
    {
        foreach ($eventData as $eventField => &$fieldValue) {
            if (is_array($fieldValue)) {
                $fieldValue = $this->prepareData($fieldValue);
            } else {
                $fieldValue = $fieldValue; //Html::encode($fieldValue);
            }
        }
        return $eventData;
    }
}