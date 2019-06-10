<?php
namespace backend\models;


use common\models\promo\PromoBannerImage;
use common\models\UserSocial;
use yii\base\Model;
use common\models\UsersDocumentsUploaded;

/**
 * Signup form
 */
class PromoBannerImageForm extends Model
{
    //user
    public $sizex;
    public $sizey;
    public $is_main;
 //   public $imageFiles;
    public $link;
    public $promo_banner_id;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sizex','sizey', 'link'], 'safe'],
            [['sizex','sizey', 'link'], 'required'],
            [['sizex','sizey'], 'integer', 'min' => 0],
           // [['imageFiles'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, gif, jpeg, bmp', 'maxFiles' => 50],
            [['is_main'], 'boolean', 'trueValue' => '1', 'falseValue' => '0'],
            [['is_main'], 'default', 'value' => '0'],
        ];
    }


    public function saveChange( $banner_image )
    {

        if (!$this->validate()) {
            var_dump($this);
            var_dump($this->errors);
            die;
            return null;
        }
        $path = dirname(dirname(__DIR__)).'/frontend/web';
        if($this->link[0] != '/') {
            $this->link = substr_replace($this->link, '/', 0, 0);
        }
        var_dump($this->link);
        die;
        if(!is_file($path.$this->link)) {
            $this->addError('link', 'Файла по указаному адрессу не существует');
            return false;
        }


        //user
        $banner_image->is_main = $this->is_main;
        $banner_image->promo_banner_id = $this->promo_banner_id;
        $banner_image->html_size = 'width="'.$this->sizex.'" height="'.$this->sizey.'"';
        $banner_image->size = $this->sizex.'x'.$this->sizey;
        $banner_image->link = $this->link;
        if($banner_image->is_main) {
            PromoBannerImage::updateAll(['is_main' => false,['promo_banner_id' => $this->promo_banner_id, 'size' => $banner_image->size, 'type' => 'html']]);
        }

        return $banner_image->save();

    }

    /** заполнит данные формы
     * @return array
     */
//    public function getSelectValue( $banner_image ){
//
//        $this->sizex = $user->date_bithday;
//        $this->sizey = $user->firstname;
//        $this->lastname = $user->lastname;
//        $this->middlename = $user->middlename;
//        $this->phone = $user->phone;
//        $this->email = $user->email;
//        $this->username = $user->username;
//
//        $this->country_id = $user->country_id;
//        $this->city_name = $user->city_name;
//
//        $this->facebook = $social->facebook;
//        $this->vk = $social->vk;
//        $this->instagram = $social->instagram;
//        $this->skype = $social->skype;
//        $this->whatsapp = $social->whatsapp;
//        $this->twitter = $social->twitter;
//
//        $this->manager_id = $user->manager_card_id;
//    }



}
