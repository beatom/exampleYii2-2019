<?php

namespace common\service;

use Yii;

class LogMy{
    private static $instance = null;
    private static $path = null;

    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    private function __construct() {}
    private function __clone() {}

    public function setLog( $arr, $filename ){
        ob_start();
        var_dump('-------------------');
        var_dump(date('Y-m-d H:i:s'));

        foreach ($arr as $key=>$value){
            var_dump($key);
            var_dump($value);
        }

        $text = ob_get_clean();
        $fp = fopen(Yii::getAlias('@rootdir')."/frontend/runtime/logs/".$filename.".txt", "a+");
        fwrite($fp, $text);
        fclose($fp);
    }
}

