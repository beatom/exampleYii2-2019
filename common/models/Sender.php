<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;
use common\service\Servis;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property string $surname
 * @property string $position
 * @property string $avatar
 *
 */
class Sender extends ActiveRecord
{
    public $avatar_file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sender';
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
            [['name', 'surname', 'position'], 'trim'],
            [['name', 'surname', 'position'], 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            [['name', 'surname', 'position', 'avatar'], 'string', 'min' => 3, 'max' => 255, 'tooShort' => Yii::t('app','Не меньше трех символов')],
           // ['avatar_file', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, bmp, gif'],
            //['avatar', 'required', 'message' => 'Загрузите изображение']
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
    
    public function saveModel(){
       if(!$this->validate()) {
           return false;
       }

        if(isset($this->avatar_file) AND $this->avatar_file != '') {
            $newPath = static::realizePath('sender');
            $avatar_name = Servis::getInstance()->randomCode(12);
            $filename = $avatar_name . '.' . $this->avatar_file->extension;
            $path = Yii::getAlias('@rootdir').'/frontend/web/upload/sender/'. $newPath .$filename;
            $this->avatar_file->saveAs($path);
            //Servis::cropImage($path, dirname(__DIR__).'/web/upload/sender/'. $newPath . $avatar_name .'_cropped.'.$this->avatar_file->extension);
           // unlink($path);
            $this->avatar =  '/upload/sender/'. $newPath . $avatar_name .'.'.$this->avatar_file->extension;
            //$user->avatar = static::saveImage($this->avatar, 'avatars')['image'];
        }

        return $this->save();
    }


       static function  realizePath($base){
           $pathToUpload = Yii::getAlias('@rootdir').'/frontend/web/upload/';
           if(!is_dir($pathToUpload)) {
               mkdir($pathToUpload, 0755);
           }

           $pathToUpload = Yii::getAlias('@rootdir').'/frontend/web/upload/'.$base.'/';
           if(!is_dir($pathToUpload)) {
               mkdir($pathToUpload, 0755);
           }

           $path1 = Servis::getInstance()->randomCode();
           $path2 = Servis::getInstance()->randomCode();

           if(!is_dir($pathToUpload. $path1)) {
               mkdir($pathToUpload. $path1, 0755);
           }
           if(!is_dir($pathToUpload. $path1.'/'.$path2)) {
               mkdir($pathToUpload. $path1.'/'.$path2, 0755);
           }
           return $path1.'/'.$path2 . '/';
       }

}
