<?php
namespace console\controllers;

use common\service\PusherService;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use console\models\EventPusher;
use React\Socket\SecureServer;
use yii\console\Controller;
use React\EventLoop\Factory;
use React\ZMQ\Context;
use React\Socket\Server;
use Ratchet\Wamp\WampServer;
use React\EventLoop\LoopInterface;
use Yii;

class SocketController extends Controller
{
    const STATISTIC_MUTEX_NAME = 'cron_statistic';

    public function actionStartServer()
    {
        if(!Yii::$app->get('mutex')->acquire(static::STATISTIC_MUTEX_NAME)) {
            var_dump('locked');
            return false;
        }
        $loop = Factory::create();
        // Класс, который реализуем ниже.
        $pusher = new EventPusher;

        // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new Context($loop);
        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->bind('tcp://127.0.0.1:5555');
        $pull->on('message', array($pusher, 'onPushEventData'));

        // Set up our WebSocket server for clients wanting real-time updates
        $webSock = new Server("0.0.0.0:8088/wss", $loop);
//        $webSock = new SecureServer($webSock, $loop, array(
//            'local_cert' => 'server.pem'
//        ));
        // Binding to 0.0.0.0 means remotes can connect
        $WsServer = new WsServer(
            new WampServer(
                $pusher
            )
        );

        $webServer = new IoServer(
            new HttpServer(
                $WsServer
            ),
            $webSock
        );
        //$WsServer->enableKeepAlive($loop, 10);
        echo '===== Server successfully started =====' . "\n";
//        echo '========= '.date('Y-m-d H:i:s').' =========' . "\n";
//        echo '=======================================' . "\n";
        $loop->run();
    }

    public function actionTest()
    {
//        $eventModel = ['test' => 'message'];
//        $context = new \ZMQContext();
//        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'desktop');
//        if($socket instanceof \ZMQSocket)
//        {
//            // Здесь тоже передаём идентификатор, чтобы в push классе мы смогли получить объект topic
//            $eventModel['subscribeKey'] = 'eventMonitoring';
//            $eventData = json_encode($eventModel);
//            $socket->connect("tcp://127.0.0.1:5555");
//            $socket->send($eventData);
//        }
        PusherService::getInstance()->send(['test title' => 'test message']);
    }

    public function actionDrop()
    {
        PusherService::getInstance()->drop();
    }
}