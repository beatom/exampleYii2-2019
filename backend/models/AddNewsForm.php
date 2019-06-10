<?php
namespace backend\models;

use common\service\Servis;
use yii\base\Model;
use common\models\User;
use common\models\News;

/**
 * Signup form
 */
class AddNewsForm extends Model
{
    //user
    public $id;
    public $date_add;
    public $img;
    public $title;
    public $title_en;
//    public $text_small;
//    public $text_small_en;
    public $text_big;
    public $text_big_en;
    public $synonym;
    public $status;
    public $meta_title;
    public $meta_description;
    public $meta_keyword;
    public $delimage;
    
    public $cat;
    public $cat_en;
    public $from;
    public $from_en;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title',
//                'text_small',
                'text_big','title_en',
//                'text_small_en',
                'text_big_en', 'status', 'cat', 'cat_en', 'from', 'from_en'], 'required'],
            [['synonym', 'img','date_add', 'meta_keyword','meta_description','meta_title', 'delimage'],'safe'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function addNews( $add = true, $news=null )
    {
        if (!$this->validate()) {
            return null;
        }

        if( $add ){
            $news = new News();
            if( !$this->date_add) {
	            $news->date_add = date( 'Y-m-d H:i:s', time() );
            }
        } else{
	        $news->date_add = $this->date_add;
        }

        if( !$this->synonym ){
            $this->synonym = Servis::getInstance()->rusToLat($this->title);
        }
        
        $news->title = $this->title;
        $news->title_en = $this->title_en;
//        $news->text_small = $this->text_small;
//        $news->text_small_en = $this->text_small_en;
        $news->text_big = $this->text_big;
        $news->text_big_en = $this->text_big_en;
        $news->synonym = $this->synonym;
        $news->status = $this->status;
        $news->meta_description = $this->meta_description;
        $news->meta_keyword = $this->meta_keyword;
        $news->meta_title = $this->meta_title;

        $news->from = $this->from;
        $news->cat = $this->cat;
        $news->cat_en = $this->cat_en;
        $news->from_en = $this->from_en;

        if($this->delimage){
            $news->img = '';
        }

        $news->save();

        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/../../frontend/web/upload/news/';
        if(!file_exists($uploaddir)){
            mkdir($uploaddir, 0755);
        }

        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/../../frontend/web/upload/news/'.$news->id.'/';

        if(!file_exists($uploaddir)){
            mkdir($uploaddir, 0755);
        }

        $name =$_FILES['AddNewsForm']['name']['img'];
        $uploadfile = $uploaddir . basename($name);

        if (move_uploaded_file($_FILES['AddNewsForm']['tmp_name']['img'], $uploadfile)) {

            $news->img =  '/upload/news/'.$news->getId().'/'.$name;

        }

        return  $news->save();
    }

    public function setData($news){
        $this->img = $news->img;
        $this->date_add = date( 'Y-m-d', strtotime( $news->date_add));
        $this->title = $news->title;
        $this->title_en = $news->title_en;
//        $this->text_small = $news->text_small;
//        $this->text_small_en = $news->text_small_en;
        $this->text_big = $news->text_big;
        $this->text_big_en = $news->text_big_en;
        $this->synonym = $news->synonym;
        $this->status = $news->status;
        $this->meta_description = $news->meta_description;
        $this->meta_keyword = $news->meta_keyword;
        $this->meta_title = $news->meta_title;

        $this->from = $news->from;
        $this->cat = $news->cat;
        $this->cat_en = $news->cat_en;
        $this->from_en = $news->from_en;
        
    }
}
