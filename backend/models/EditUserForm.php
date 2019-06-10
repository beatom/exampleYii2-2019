<?php
namespace backend\models;

use common\models\UserDoc;
use common\models\UserSocial;
use yii\base\Model;
use common\models\User;
use common\service\Servis;
use common\models\UsersDocumentsUploaded;
use Yii;
/**
 * Signup form
 */
class EditUserForm extends Model
{
    //user
    public $username;
    public $email;
    public $phone;
    public $avatar;

    public $date_birthday;

    public $firstname;
    public $lastname;
    public $middlename;

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

    public $manager_id;
    public $vip;
    
    public $role;
    public $promo_code;
    public $invitation_code;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['birthday','firstname','lastname','middlename', 'phone', 'email', 'avatar', 'manager_id',
                'facebook','vk','instagram','skype','whatsapp','twitter', 'username', 'city_name', 'country_id', 'role'], 'safe'],
            ['username', 'match', 'pattern' => '/^[a-z0-9_]+$/i', 'message' => 'Ник должен состоять только из латинских букв и/или цифр'],
            ['email', 'email'],
            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, bmp, gif'],
            //['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','Пользователь с таким e-mail уже существует')],
            ['date_birthday', 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['phone', 'email', 'username', 'promo_code', 'invitation_code'], 'required', 'message' => 'Необходимо заполнить'],
            [['firstname', 'lastname', 'middlename'], 'string'],
            [['promo_code', 'invitation_code'], 'string', 'min' => 5, 'tooShort' => 'Не меньше 5 символов'],
            ['promo_code', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','Этот пригласительный код уже занят'), 'filter' => 'username <> "'.$this->username.'"'],
            ['invitation_code', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','Эта пригласительная ссылка уже занята'), 'filter' => 'username <> "'.$this->username.'"'],
            ['vip', 'boolean'],
        ];
    }


    public function saveChange( $user, $social )
    {

        if (!$this->validate()) {
            return false;
        }
        //user
        $user->username = $this->username;
        $user->phone = $this->phone;
        $user->email = $this->email;
        $user->vip  = (isset($this->vip) AND $this->vip == true) ? true : false;
        
        $user->firstname = $this->firstname;
        $user->lastname = $this->lastname;
        $user->middlename = $this->middlename;
        $user->country_id = $this->country_id;
        $user->city_name= $this->city_name ;
        $user->date_bithday = $this->date_birthday;
        $user->manager_card_id = $this->manager_id;

        $user->promo_code = $this->promo_code;
        $user->invitation_code = $this->invitation_code;

        if(isset($this->role) AND $this->role AND !empty($this->role)) {
            $auth = Yii::$app->authManager;
            foreach ($auth->getRolesByUser($user->id) as $role) {
                $auth->revoke($role, $user->id);
            }
            foreach ($this->role as $new_role) {
                $role = $auth->getRole($new_role);
                $auth->assign($role, $user->id);
            }

        }

        if($this->avatar) {
            $newPath = static::realizePath('avatars');
            $avatar_name = Servis::getInstance()->randomCode(12);
            $filename = $avatar_name . '.' . $this->avatar->extension;
            $path = dirname(__DIR__).'/../frontend/web/upload/avatars/'. $newPath .$filename;
            $this->avatar->saveAs($path);
            Servis::cropImage($path, dirname(__DIR__).'/../frontend/web/upload/avatars/'. $newPath . $avatar_name .'_cropped.'.$this->avatar->extension);
            unlink($path);
            $user->avatar =  '/upload/avatars/'. $newPath . $avatar_name .'_cropped.'.$this->avatar->extension;
        }
        $this->avatar = $user->avatar;
        if(!$user->save()) {
            return false;
        }
        $out['user'] = $user;
        //social
        foreach ( UserSocial::$arr_social as $item ){
            $social->$item = $this->$item;
        }
        $out['social'] = ($social->save())? $social : null;

        return $out;
    }

    /** заполнит данные формы
     * @return array
     */
    public function getSelectValue( $user, $social ){
        $auth = Yii::$app->authManager;
        $this->date_birthday = $user->date_bithday;

        $this->firstname = $user->firstname;
        $this->lastname = $user->lastname;
        $this->middlename = $user->middlename;
        $this->avatar = $user->avatar;
        $this->phone = $user->phone;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->vip = $user->vip;
        $this->country_id = $user->country_id;
        $this->city_name = $user->city_name;

        $this->facebook = $social->facebook;
        $this->vk = $social->vk;
        $this->instagram = $social->instagram;
        $this->skype = $social->skype;
        $this->whatsapp = $social->whatsapp;
        $this->twitter = $social->twitter;
        $this->telegram = $social->telegram;

        $this->promo_code = $user->promo_code;
        $this->invitation_code = $user->invitation_code;

        $user_roles = $auth->getRolesByUser($user->id);
        foreach ($user_roles as $key => $role) {
            $this->role[] = $key;
        }

        $this->manager_id = $user->manager_card_id;
    }


    static function  realizePath($base){
        $pathToUpload = dirname(__DIR__).'/../frontend/web/upload/'.$base.'/';
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
