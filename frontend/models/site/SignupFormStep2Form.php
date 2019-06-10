<?php
namespace frontend\models\site;

use common\models\BalanceBonusLog;
use common\models\SmsManager;
use yii\base\Model;
use common\models\User;
use common\models\LogConfirm;
use common\models\EmailTemplate;
use common\models\ChatTemplate;
use  Yii;
use common\models\UserPartnerInfo;
use common\models\UserIpLog;

/**
 * Signup form
 */
class SignupFormStep2Form extends Model
{
    public $username;
    public $firstname;
    public $email;
    public $phone;
    public $password;
    public $sms_code;
    public $promo_code;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','Этот ник уже занят')],
            ['username', 'string', 'min' => 2, 'max' => 255, 'tooShort' => Yii::t('app','Логин не должно быть короче двух символов')],
            ['username', 'match', 'pattern' => '/^[a-z0-9_]+$/i', 'message' => 'Ник должен состоять только из латинских букв и/или цифр'],

            ['firstname', 'trim'],
            ['firstname', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['firstname', 'string', 'min' => 2, 'max' => 255, 'tooShort' => Yii::t('app','Имя не должно быть короче двух символов')],

            ['email', 'trim'],
            ['email', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Этот email уже используется.'],

            ['phone', 'trim'],
            ['phone', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['phone', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app','Этот номер телефона уже закреплен за другим пользователем')],
            ['phone', 'string', 'max' => 255],

            ['password', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['password', 'string', 'min' => 6],

            ['sms_code', 'trim'],
            ['sms_code', 'required', 'when' => function() {
                return SmsManager::getActiveSmsProvider() ? true : false;
            }, 'message'=> Yii::t('app','Необходимо заполнить')],

            [['promo_code','username','phone','password','email', 'firstname'], 'safe'],


        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $phone = User::clearPhone( $this->phone );
        $confirm = LogConfirm::find()->orderBy(' date_add DESC')->where('phone = '.$phone.' AND date_add > '. (time() - 600 ))->limit(1)->all();
        $sms_active = false;
        //вернуть false
        if(SmsManager::getActiveSmsProvider()) {
            $sms_active = true;
            $confirm_phone = false;
            foreach ($confirm as $item ){
                if($this->sms_code == $item->code){
                    $confirm_phone = true;
                }
            }
            if(!$confirm_phone){
                $this->addError('sms_code', Yii::t('app','СМС КОД НЕВЕРНЫЙ'));
                return null;
            }
        }
        $user_partner = false;
        if(!empty($this->promo_code) AND !$user_partner = User::find()->where(['promo_code' => $this->promo_code])->one()){
            $this->addError('promo_code', Yii::t('app','Промо код не найден'));
            $this->promo_code = null;
            return null;
        } elseif($invite = Yii::$app->request->cookies->getValue('invite')) {
            $user_partner = User::find()->where(['invitation_code' => $invite])->one();
        }

        $user = new User();
        $res = $user->createUser($this->username, $this->email, $this->password);
        if($res){

            UserIpLog::setLog($user);

            $phone = User::clearPhone( $this->phone);
            $user->phone = $phone;
            $user->firstname = $this->firstname;
            $user->sms_confirm = $sms_active ? true : false;
            if($user_partner){
                $user->partner_id = $user_partner->id;
                if($this->promo_code == $user_partner->promo_code) {
                    $user->promo_used = true;
                }
            }



            $user->save();
            $user->sendPulseNewUserNotify();

            foreach ($confirm as $item ){
                $item->user_id = $user->id;
                $item->save();
            }

            if($user->partner_id) {
                UserPartnerInfo::addPartner($user->getId(), $user->partner_id);
            }

            //send email
//            $data = [];
//            $data['user_name'] = $user->username;
//            $data['password'] = $this->password;
//            $data['partner'] = $user_partner ? $user_partner->username;

            // $email_template = EmailTemplate::findIdentity(EmailTemplate::REGISTER_USER );
            //$res = $email_template->getEmailTemplate( $data );
            //$email_template->sendMail($user->email, $res['title'], $res );

            //send message
            ChatTemplate::sendMessageFromTemplate($user->id,ChatTemplate::templateRegistration);
            return $user;
        }

        return false;
    }

}
