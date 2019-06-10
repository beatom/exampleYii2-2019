<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use common\models\Banner;
use common\service\Servis;
/**
 * Signup form
 */
class BannerForm extends Model
{
    public $id;
    public $img;
    public $img_en;
    public $text_button;
    public $text_button_en;
    public $url;
    public $position;
    public $status;
    public $title;
    public $title_en;
    public $subtitle;
    public $subtitle_en;
    public $text_1;
    public $text_1_en;
    public $text_2;
    public $text_2_en;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'text_button', 'url'], 'required'],
            [['position', 'status', 'subtitle', 'text_1', 'text_2',
              'title_en', 'text_button_en', 'subtitle_en', 'text_1_en', 'text_2_en'], 'safe']
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function add( $add = true, $banner=null )
    {
        if (!$this->validate()) {
            return null;
        }

        if( $add ){
            $banner = new Banner();
        }

        $banner->title = $this->title;
        $banner->title_en = $this->title_en;
        $banner->text_1 = $this->text_1;
        $banner->text_1_en = $this->text_1_en;
        $banner->text_2 = $this->text_2;
        $banner->text_2_en = $this->text_2_en;
        $banner->text_button = $this->text_button;
        $banner->text_button_en = $this->text_button_en;
        $banner->subtitle = $this->subtitle;
        $banner->subtitle_en = $this->subtitle_en;
        $banner->url = $this->url;
        $banner->position = $this->position;
        $banner->status = $this->status;

        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/../../frontend/web/upload/slide/';

        $name = Servis::getInstance()->randomCode(12);

        $uploadfile = $uploaddir . basename($name);

        if (isset($_FILES['BannerForm']['tmp_name']['img']) AND move_uploaded_file($_FILES['BannerForm']['tmp_name']['img'], $uploadfile)) {

            $banner->img =  '/upload/slide/'.$name;
        }
        if (isset($_FILES['BannerForm']['tmp_name']['img_en']) AND move_uploaded_file($_FILES['BannerForm']['tmp_name']['img_en'], $uploadfile)) {

            $banner->img_en =  '/upload/slide/'.$name;
        }
        
        return $banner->save();
    }

    public function setData($banner){

        $this->img = $banner->img;
        $this->img_en = $banner->img_en;
        $this->title = $banner->title;
        $this->title_en = $banner->title_en;
        $this->text_1 = $banner->text_1;
        $this->text_1_en = $banner->text_1_en;
        $this->text_2 = $banner->text_2;
        $this->text_2_en = $banner->text_2_en;
        $this->text_button = $banner->text_button;
        $this->text_button_en = $banner->text_button_en;
        $this->url = $banner->url;
        $this->subtitle = $banner->subtitle;
        $this->subtitle_en = $banner->subtitle_en;
        $this->position = $banner->position;
        $this->status = $banner->status;
    }

}
