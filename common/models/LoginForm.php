<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\UserIpLog;
use yii\web\Cookie;
use common\models\SmsManager;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $phone = '79286130866';
    public $sms_code;
    public $rememberMe = true;
    public $stage = 1;
    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['sms_code', 'trim'],
            ['sms_code', 'string'],
            ['stage', 'integer'],
            ['sms_code', 'required', 'when' => function() {
                return (SmsManager::getActiveSmsProvider() AND $this->stage == 2) ? true : false;
            }, 'message'=> Yii::t('app','Необходимо заполнить')],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, Yii::t('app','Пароль неверный'));
            }
        }
    }

    public function validateCode()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $confirm = LogConfirm::find()->orderBy(' date_add DESC')->where('phone = '.$this->phone.' AND date_add > '. (time() - 300 ))->limit(1)->one();
        if($confirm->code != $this->sms_code) {
            $this->addError('sms_code', "Неверный код");
            return false;
        }
        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     * $admin_login
     * @return bool whether the user is logged in successfully
     */
    public function login($admin_login = false)
    {
        if ($this->validate()) {

            if(!$user = User::findByUsername($this->username)) {
                $this->addError('username', 'Пользователь не найден');
                return false;
            }

            if($admin_login) {
                $res = Yii::$app->user->login($user, $this->rememberMe ? 3600 * 2: 0);
            } else {
                $res = Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24: 0);
            }

            if($res){
                Yii::$app->response->cookies->add(new Cookie([
                    'name' => 'user_authenticate',
                    'value' => true,
                    'httpOnly' => false,
                    'expire' => time() + 1800,
                ]));
                //$user->popup_banner_shown = false;
                $user->unlogin = false;
                $user->save();

                UserIpLog::setLog($user);
                
                return true;
            }
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
