<?php
namespace frontend\models;

use common\models\UserDoc;
use common\models\UserSocial;
use yii\base\Model;
use common\models\User;
use common\service\Servis;
use common\models\UsersDocumentsUploaded;
use yii\web\UploadedFile;
use Yii;
/**
 * Signup form
 */
class EditUserFrom extends Model
{
    //user
//    public $username;
//    public $email;
    public $phone;
    public $password;
//    public $birth_day;
//    public $birth_mount;
//    public $birth_year;
    public $firstname;
    public $lastname;
    public $middlename;
    public $avatar;
    public $pasport_1;
//    public $pasport_2;
    public $country_id;
    public $city_name;

    //social
    public $facebook;
    public $vk;
    public $instagram;
    public $skype;
    public $whatsapp;
    public $twitter;
    public $telegram;

    public $payment_system;
    public $payment_address;
    public $sms_code;


//    public $arr_birthday;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname','lastname','middlename', 'phone',  'avatar',
                'facebook','vk','instagram','skype','whatsapp','twitter', 'city_name', 'country_id',
                'pasport_1', 'payment_system', 'telegram'], 'safe'],
            [[ 'pasport_1', 'avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, bmp, gif'],


            //[['firstname', 'lastname', 'middlename', 'city_name'], 'match', 'pattern' => '/^[а-яА-Яa-zA-Z0-9]+$/u', 'message'=>'{attribute} указано неверно.'],

            [['phone', 'sms_code'], 'trim'],
            [['phone', 'sms_code', 'payment_address', 'payment_system'], 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
           // ['phone', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','Этот номер телефона уже закреплен за другим пользователем22')],

            ['phone', 'match', 'pattern' => '/^[+0-9_]+$/i', 'message' => 'Неправильный телефон'],
            ['payment_system', 'integer', 'min' => 0],
            ['payment_address', 'string', 'min' => 5, 'message' => Yii::t('app','Необходимо заполнить'), 'tooShort' => Yii::t('app','Не меньше 5 символов')],
            [['firstname','lastname','middlename', 'city_name', 'country_id'], 'required', 'message' => 'Необходимо заполнить', 'when' => function ($model) {
                return $model->pasport_1 != null;
                        }, 'whenClient' => "function (attribute, value) {
                    return $('[name=\"EditUserFrom[pasport_1]\"]').val() != '';
                }"],

        ];
    }

    public function attributeLabels()
    {
        return [
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'middlename' => 'Отчество',
            'city_name' => 'Название города'
        ];
    }


    public function saveChange( $user, $social )
    {

        if (!$this->validate()) {
            return null;
        }

        $showmsg = false;
        
        //user
        if(isset($this->username) AND $this->username != '' AND $user->edit_username) {
            $user->username = $this->username;
            $user->edit_username = 0;
        }
        if(isset($this->phone) AND $this->phone != '' AND  $user->phone != $this->phone) {
            $user->phone = User::clearPhone($this->phone);
            $user->sms_confirm = false;
        }
        if(isset($this->email) AND $this->email != '' AND  $user->email != $this->email) {
            $user->email = $this->email;
            $user->email_confirm = false;
        }
        $user->firstname = $this->firstname;
        $user->lastname = $this->lastname;
        $user->middlename = $this->middlename;
        $user->country_id = $this->country_id;
        $user->city_name= $this->city_name ;
      //  $user->payment_system = $this->payment_system;
      //  $user->avatar = $user->getAvatarWidget();

        if(isset($this->avatar) AND $this->avatar != '') {
            $newPath = static::realizePath('avatars');
            $avatar_name = Servis::getInstance()->randomCode(12);
            $filename = $avatar_name . '.' . $this->avatar->extension;
            $path = dirname(__DIR__).'/web/upload/avatars/'. $newPath .$filename;
            $this->avatar->saveAs($path);
            Servis::cropImage($path, dirname(__DIR__).'/web/upload/avatars/'. $newPath . $avatar_name .'_cropped.'.$this->avatar->extension);
            unlink($path);
            $user->avatar =  '/upload/avatars/'. $newPath . $avatar_name .'_cropped.'.$this->avatar->extension;
            //$user->avatar = static::saveImage($this->avatar, 'avatars')['image'];
        }

        if(!empty($this->birth_year) && !empty($this->birth_mount) && !empty($this->birth_day)){
            $user->date_bithday = $this->birth_year .'-'.$this->birth_mount.'-'.$this->birth_day;
        }

        $docs = UserDoc::find()->where(['user_id' => $user->id])->one();

        $needVerification = array(1 => false, 2 => false);

        if(isset($this->pasport_1) AND $this->pasport_1 != null) {
           // $s = static::saveImage($this->pasport_1, 'user_doc');

            $newPath = static::realizePath('user_doc');
            $filename = $this->pasport_1->baseName . '.' . $this->pasport_1->extension;
            $path = dirname(__DIR__).'/web/upload/user_doc/'. $newPath .$filename;
            $this->pasport_1->saveAs($path);

            $needVerification[1] = true;
            $image_size = getimagesize($path);
            Servis::cropImage($path, dirname(__DIR__).'/web/upload/user_doc/'. $newPath . $this->pasport_1->baseName .'_cropped.'.$this->pasport_1->extension, $image_size[0]+1, $image_size[1]+1);
            unlink($path);
            $docs->pasport_1 =  '/upload/user_doc/'. $newPath . $this->pasport_1->baseName .'_cropped.'.$this->pasport_1->extension;

            UsersDocumentsUploaded::add($user->id, $docs->pasport_1);
        }
        if(isset($this->pasport_2) AND $this->pasport_2 != null) {
           // $s = static::saveImage($this->pasport_2, 'user_doc');

            $newPath = static::realizePath('user_doc');
            $filename = $this->pasport_2->baseName . '.' . $this->pasport_2->extension;
            $path = dirname(__DIR__).'/web/upload/user_doc/'. $newPath .$filename;
            $this->pasport_2->saveAs($path);
            $needVerification[2] = true;

            $image_size = getimagesize($path);
            Servis::cropImage($path, dirname(__DIR__).'/web/upload/user_doc/'. $newPath . $this->pasport_2->baseName .'_cropped.'.$this->pasport_2->extension, $image_size[0]+1, $image_size[1]+1);
            unlink($path);
            $docs->pasport_2 =  '/upload/user_doc/'. $newPath . $this->pasport_2->baseName .'_cropped.'.$this->pasport_2->extension;

            UsersDocumentsUploaded::add($user->id, $docs->pasport_2);
        }

        if( $needVerification[1] OR $needVerification[2]) {
            $docs->need_verification = true;
            $showmsg = Yii::t('app', 'Документ отправлен!');
        }
        $docs->date_add = date('Y-m-d H:i:s');
        $docs->save();
        $user->save();
        $out['user'] = $user;
        //social
        foreach ( UserSocial::$arr_social as $item ){
            if(trim($this->$item) != '') {
                $social->$item = str_replace(['>','<'],null,$this->$item);
            }
        }
        $out['social'] = ($social->save())? $social : null;
        $out['showmsg'] = $showmsg;
        return $out;
    }

    /** заполнит данные формы
     * @return array
     */
    public function getSelectValue( $user, $social ){

//        $this->birth_day = $user->date_bithday_arr['day'];
//        $this->birth_mount = $user->date_bithday_arr['mount'];
//        $this->birth_year = $user->date_bithday_arr['year'];
        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->middlename = $user->middlename;
        $this->avatar = $user->avatar;
        $this->phone = $user->phone;
//        $this->email = $user->email;
//        $this->username = $user->username;
        $this->country_id = $user->country_id;
        $this->city_name = $user->city_name;

        $this->facebook = $social->facebook;
        $this->vk = $social->vk;
        $this->instagram = $social->instagram;
        $this->skype = $social->skype;
        $this->whatsapp = $social->whatsapp;
        $this->twitter = $social->twitter;
        $this->telegram = $social->telegram;

        $this->payment_system = $user->payment_system;
        $this->payment_address = $user->payment_address;

//        $this->arr_birthday = [];
//        for($i=1; $i<32;$i++){
//            $val = $i;
//            if($i<10)
//                $val = '0'.$i;
//            $this->arr_birthday['days'][$val] = $val;
//        }
//        for($i=1; $i<13;$i++){
//            $val = $i;
//            if($i<10)
//                $val = '0'.$i;
//            $this->arr_birthday['mounts'][$val] = $val;
//        }
//        for($i=date("Y"); $i > 1920 ;$i--){
//            $this->arr_birthday['years'][$i] = $i;
//        }
    }

//    public function upload()
//    {
//        if ($this->validate()) {
//            $this->avatar->saveAs('uploads/' . $this->avatar->baseName . '.' . $this->avatar->extension);
//            return true;
//        } else {
//            return false;
//        }
//    }

    public function upload()
    {
        if ($this->validate()) {
            $this->pasport_1->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
    

    static function  realizePath($base){
        $pathToUpload = dirname(__DIR__).'/web/upload/';
        if(!is_dir($pathToUpload)) {
            mkdir($pathToUpload, 0755);
        }

        $pathToUpload = dirname(__DIR__).'/web/upload/'.$base.'/';
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

    static function saveImage($image, $base) {
        $response['image'] =  $image;
        $response['need_verification'] = false;
        $data = $image;
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            if (in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                $data = base64_decode($data);
                if (!$data === false) {
                    $name = Servis::getInstance()->randomCode(12);
                    $filename = $name.'.'.$type;
                    $newPath = static::realizePath($base);
                    $path = dirname(__DIR__).'/web/upload/'.$base.'/'. $newPath .$filename;
                    file_put_contents($path, $data);
                    if($base == 'avatars') {
                        Servis::cropImage($path, dirname(__DIR__).'/web/upload/'.$base.'/'. $newPath . $name .'_cropped.'.$type);
                        $response['image'] =  '/upload/'.$base.'/'. $newPath . $name .'_cropped.'.$type;
                    } else {
                        $response['base_imege'] = $image;
                        $response['image'] =  '/upload/'.$base.'/'. $newPath . $filename;
                    }

                    $response['need_verification'] = true;
                }
            }
        }

        return $response;
    }

}
