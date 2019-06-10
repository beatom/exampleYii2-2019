<?php

namespace common\service\api;

use common\models\AmoCustomField;
use common\models\AmoQueue;
use common\models\BalanceLog;
use common\models\Country;
use common\models\ManagerCard;
use common\models\User;
use PHPUnit\Framework\Exception;
use common\models\Options;
use common\service\LogMy;

class SendPulse
{
    private static $instance = null;
    
    private $sendPulse_id = 'f85c4cf4961db968dd2edcb0fe598fad';
    private $sendPulse_secret = 'c022432ce410136849f224df1936346e';

    private $access_token = false;


    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function curl($task, $data)
    {
        $credentials_data = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $this->sendPulse_id,
            'client_secret' => $this->sendPulse_secret
        ]);

        if(!$this->access_token) {
            $url =  'https://api.sendpulse.com/oauth/access_token';
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $credentials_data);
            $out = json_decode(curl_exec($curl), true);
            curl_close($curl);

            if(!isset($out['access_token'])) {
                return false;
            } 
            $this->access_token = $out['access_token'];
        }
       


        $headers = array(
            'Content-Type: text/plain',
            sprintf('Authorization: Bearer %s', $this->access_token)
        );

        $url = 'https://events.sendpulse.com' . $task;
        if($task == '/events/name/weekly_reports') {
            foreach ($data as $d) {
                $d = json_encode($d);
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $d);
                $out = json_decode(curl_exec($curl), true);
                curl_close($curl);

                \Yii::info(['name' => 'getFileContent ответ',
                    'url' => $url,
                    'data' => $d,
                    'response' => $out,
                ], 'sendpulse');
            }
        } else {
            $data = json_encode($data);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $out = json_decode(curl_exec($curl), true);
            curl_close($curl);

            \Yii::info(['name' => 'getFileContent ответ',
                'url' => $url,
                'data' => $data,
                'response' => $out,
            ], 'sendpulse');
        }

        return $out;
    }
}
