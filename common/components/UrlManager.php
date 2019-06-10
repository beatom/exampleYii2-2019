<?php
/*
 * Добавляет указатель языка в ссылки
 */
namespace common\components;

use Yii;

class UrlManager extends \yii\web\UrlManager {

    public $languages = 'languages/default/index';

    public function createUrl($params) {

        //Получаем сформированную ссылку(без идентификатора языка)
        $url = parent::createUrl($params);

        if ( (! empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') ||
            (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (! empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443') ) {
            $server_request_scheme = 'https';
        } else {
            $server_request_scheme = 'http';
        }

        //для аджакса, не хочет разбирать
        if(isset($params['url']) && $params['url'] != $server_request_scheme . '://'.$_SERVER['SERVER_NAME'].'/ru' && in_array( 'languages/default/index', $params)){
            $tmp = str_replace('/ru/', '/', $params['url']);
            $leng = strlen($tmp);
            if(substr($tmp, $leng-3) == '/ru'){
                $tmp = substr($tmp, 0, $leng-3);
            }
            return $tmp;
        }

        if (empty($params['lang'])) {
            //текущий язык приложения
            $curentLang = Yii::$app->language;

            //Добавляем к URL префикс - буквенный идентификатор языка
            if ($url == '/') {
                return '/' . $curentLang;
            } else {
                return '/' . $curentLang . $url;
            }
        };

        return $url;
    }
}