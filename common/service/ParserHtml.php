<?php

namespace common\service;



class ParserHtml
{
    private static $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct(){}
    private function __clone(){}

    public function getDataHTML($html){

        $html = str_replace(' class="active"', '', $html);
        $matches = [];
        preg_match_all('/<tr>(.*?)<\/tr>/s', $html, $matches);

        $tmp = (isset($matches[0]))? $matches[0] : [];
        $matches = [];
        $out = [];
        foreach ($tmp as $item){
            preg_match_all('/<td>(.*?)<\/td>/s', $item, $matches);

            $temp = (isset($matches[0]))? $matches[0] : [];
            $t = [];
            foreach ($temp as $value){
                $t[] = strip_tags($value);
            }
            $out[] = $t;
        }
        
        return $out;
    }

    public function getDataHtml2Table( $html ){

        $html = str_replace(' class="active"', '', $html);
        $matches = [];
        preg_match_all('/<table(.*?)<\/table>/s', $html, $matches);

        $symbol = $matches[0][0];

        $pos = strpos($symbol, '<td colspan=4>')+14;
        if($pos){
            $pos_end = strpos($symbol, ' (', $pos);
            $symbol = substr($symbol, $pos, $pos_end - $pos );
        }

        $table = $matches[0][1];
        $table = str_replace(' bgcolor="#E0E0E0" align=right', '', $table);
        $table = str_replace(' align=right', '', $table);

        preg_match_all('/<tr>(.*?)<\/tr>/s', $table, $matches);

        $tmp = (isset($matches[0]))? $matches[0] : [];
        $matches = [];
        $out = [];
        foreach ($tmp as $item){
            preg_match_all('/<td(.*?)<\/td>/s', $item, $matches);

            $temp = (isset($matches[0]))? $matches[0] : [];
            $t = [];
            foreach ($temp as $value){
                $t[] = strip_tags($value);
            }
            $out[] = $t;
        }


        return ['data' => $out, 'symbol'=>$symbol];

    }


}
