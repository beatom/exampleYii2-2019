<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property string $key
 * @property string $value
 * @property string $description
 * @property string $section
 *
 */
class Options extends ActiveRecord
{
    const keys_social = ['facebook', 'vk', 'twitter', 'instagram', 'youtube', 'telegram'];
    const keys_home_page_ru = ['WIDEO_ON_HOME', 'HOME_SIMPLE_AS', 'WHY_invest'];
    const keys_home_page_en = ['WIDEO_ON_HOME_EN', 'HOME_SIMPLE_AS_EN', 'WHY_invest_EN'];
    const keys_trade_static_page_ru = [ 'trede_static_page_slide'];
    const keys_trade_static_page_en = [ 'trede_static_page_slide_en'];
    const keys_partnership_ru = ['partnership_fullpage', 'partnership_hiw'];
    const keys_partnership_en = ['partnership_fullpage_en', 'partnership_hiw_en'];
    const keys_about = [ "about_best_result", "about_speed_request", "about_alternative_banks", "about_paid_month", "about_count_partner" ];

    private static $socials = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'options';
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
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @param array
     * @return array|ActiveRecord[]
     */
    public static function getOptions( $arr ){
        $where = '';
        $flag = false;
        foreach ($arr as $item){
            if($flag){
                $where .= ' OR ';
            }
            $flag = true;
            $where .= ' `key` = "'. $item.'"';
        }
        return static::find()->where($where)->all();
    }

    /**
     * получить массив соц ссылок сайта ключ значение
     * @return array
     */
    public static function getSocialLink(){

        if(static::$socials){
            return static::$socials;
        }

        $flag = false;
        $where = '';
        foreach (self::keys_social as $item){
            if($flag){
                $where .= ' OR ';
            }
            $flag = true;
            $where .= ' `key` = "'. $item.'"';
        }
        $res = static::find()->where($where)->all();
        $out = [];
        foreach ( $res as $item){
            $out[$item->key] = $item->value;
        }

        static::$socials = $out;

        return $out;
    }

    /**
     * получить массив блоков сайта ключ значение
     * @return array
     */
    public static function getOptionsAsots( $keys, $del_lang = false ){ //getOptionsAsots
        $flag = false;
        $where = '';

        foreach ( $keys as $item){
            if($flag){
                $where .= ' OR ';
            }
            $flag = true;
            $where .= ' `key` = "'. $item.'"';
        }
        $res = static::find()->where($where)->all();
        $out = [];
        foreach ( $res as $item){
            if($del_lang){
                $out[ substr($item->key, 0, (strpos($item->key, $del_lang)))] = $item->value;
            }
            else {
                $out[$item->key] = $item->value;
            }
        }
        return $out;
    }


    public static function getOptionValueByKey( $key ){
        return static::find()->where(['key'=>$key])->one()->value;
    }

    public static function setOptionValueByKey( $key, $option ){
        $res = static::find()->where(['key'=>$key])->one();
        if($res) {
            $res->value = $option;
        }
        else{
            $add = new Options();
            $add->key = $key;
            $add->value = $option;
            return $add->save();
        }
        return $res->save();
    }

}
