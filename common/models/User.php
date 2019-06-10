<?php

namespace common\models;

use common\models\logs\SendPulseNotification;
use common\models\trade\Investment;
use common\models\trade\InvestmentLog;
use common\service\api\AmoCrm;
use common\service\api\SendPulse;
use common\service\api_terminal\ApiTerminal;
use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\service\LogMy;
use YoHang88\LetterAvatar\LetterAvatar;
use common\service\Servis;
use common\models\BonusDebt;
use common\models\UserIpLog;
use common\models\ChatTemplate;
use common\models\ManagerCard;
use common\models\UserSocial;
use common\models\UserPartnerInfo;
use common\models\DaysLog;
use common\models\Events;
/**
 * User model
 *
 * @property integer $id
 * @property string $email
 * @property string $auth_key
 * @property string $pass_md5
 * @property string $password_reset_token
 * @property string $username
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property datetime $date_bithday
 * @property integer $country_id
 * @property integer $city_name
 * @property string $phone
 * @property string $avatar
 * @property integer $sms_confirm
 * @property integer $email_confirm
 * @property integer $balance
 * @property integer $balance_bonus
 * @property integer $balance_partner
 * @property integer $ball_invest
 * @property integer $partner_id
 * @property integer $status_in_partner
 * @property integer $status
 * @property datetime $date_reg
 * @property string $fb_id
 * @property string $vk_id
 * @property integer $edit_username
 * @property boolean $verified
 * @property boolean $first_bonus_recived
 * @property string $manager_card_id
 * @property integer $user_category_id
 * @property integer $amo_contact_id
 * @property integer $amo_contact_stage
 * @property boolean $vip
 * @property integer $amo_name_level
 * @property integer $amo_tag_level
 * @property string $payment_system
 * @property string $payment_address
 * @property string $invitation_code
 * @property string $promo_code
 * @property boolean $promo_used
 * @property datetime $status_open
 * @property boolean $events_notice
 * @property integer $chat_messages_count
 * @property boolean $events_complete
 * @property boolean $first_banner_shown
 * @property boolean $seven_bonus_received
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ADMIN = 1;
    const STATUS_ACTIVE = 10;
    const STATUS_MESSAGE_SENDER = 2;
    const STATUS_MESSAGE_SENDER_RECIVER = 3;
    public $total_b = null;
    public $total_b_with_p = null;
    public $first_deposit_date = null;
    public $first_deposit_sum = 0;
    public $deposit_sum = 0;
    public $withdraw_sum = 0;
    public $difference = 0;
    public $result = 0;
    public $dtp = 0;

    public static $partner_staus = [
        0 => 'Нет статуса',
//        1 => 'Новичок',
        1 => 1,//'Консультант',
        // 3 => 'Менеджер',
        2 => 2,//'Специалист',
        3 => 3,//'Профессионал',
        4 => 4,//'Эксперт',
        5 => 5,//'Топ-эксперт',
        6 => 6,//'Лидер',
        //9 => 'Топ-лидер',
        7 => 7,//'Региональный представитель',
        8 => 8,//'Премиум-партнёр',
        9 => 9,//'VIP-партнёр',
    ];

    public static $roles_names = [
        'admin' => 'Администратор',
        'manager' => 'Менеджер',
        'moderator' => 'Модератор чата',
    ];


    public $date_bithday_arr = null;
    public $investments_summ = null;

    const update_in_amocrm_field = ['username', 'phone', 'email', 'date_bithday', 'date_reg', 'firstname', 'lastname', 'middlename', 'country_id', 'city_name', 'status_in_partner', 'balance', 'balance_partner', 'manager_card_id'];

    public static $balance_sort = [
        'id' => 'id Пользователя',
        'balance' => 'Балансу пользователя',
        'investments_summ' => 'Сумме инвестиций',
        'total_b' => 'Сумме баланса и инвестиций',
        'balance_partner' => 'Партнерскому балансу',
        'total_b_with_p' => 'Сумме общей',
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);


        if ($insert) {
            $this->setInvationCodes();
            $h = date('H');
            $date = false;
            if ($h < 10) {
                $date = date('Y-m-d', strtotime('yesterday'));
            } elseif($h >= 15) {
                $date = date('Y-m-d');
            }
            if($date){
                $day_log = DaysLog::getLog($date);
                if(!empty($day_log->events_complete)) {
                    $this->events_notice = 1;
                    foreach ($day_log->events_complete as $e) {
                        if($e->result) {
                            $this->events_complete = true;
                            break;
                        }
                    }
                    $this->save();
                }
            }


        }
//        if ($this->amo_contact_id OR $insert) {
//            $data = [];
//            if ($insert) {
//                foreach ($changedAttributes as $key => $a) {
//                    if (in_array($key, static::update_in_amocrm_field) AND $a != $this->$key) {
//                        $data[$key] = $this->$key;
//                    }
//                }
//                $data['ip'] = time();
//                $data['balance'] = 0;
//                $data['first_deposit'] = 0;
//                AmoCrm::getInstance()->addUser($this, $data);
//
//            } else {
//                foreach ($changedAttributes as $key => $a) {
//                    if (in_array($key, static::update_in_amocrm_field) AND $a != $this->$key) {
//                        $data[] = $key;
//                    }
//                }
//                if (!empty($data)) {
//                    AmoCrm::getInstance()->updateUser($this, $data);
//                }
//            }
//        }
    }

    public function afterLogin($event)
    {
        return $event;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED, self::STATUS_ADMIN, self::STATUS_MESSAGE_SENDER_RECIVER, self::STATUS_MESSAGE_SENDER]],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id, $arr = false)
    {
        if ($arr)
            return static::find()->where(['id' => $id])->asArray()->one();

        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    public static function findByFB($fb_id)
    {
        return static::findOne(['fb_id' => $fb_id]);
    }

    public static function findByVK($fb_id)
    {
        return static::findOne(['vk_id' => $fb_id]);
    }

    public static function getPartners($patner_id)
    {

        return User::find()->where('partner_id = ' . $patner_id)->all();
    }

    public static function getUsersByIds($user_ids, $offset = 0)
    {

        return static::find()->where(['id' => $user_ids])->offset($offset)->limit(5)->all();


    }

    public function getSocial()
    {
        return $this->hasOne(UserSocial::class, ['user_id' => 'id']);
    }

    public function getPartner()
    {
        return $this->hasOne(static::class, ['id' => 'partner_id']);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function countInvested()
    {
        $invested = BalanceLog::find()->where(['user_id' => $this->id, 'system' => BalanceLog::$user_min_bal_systems, 'operation' => [BalanceLog::deposit, BalanceLog::present_admin], 'status' => BalanceLog::done])->sum('summ');
        return $invested ? $invested : 0;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->pass_md5);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->pass_md5 = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isAdmin()
    {
        if ($this->status == User::STATUS_ADMIN) {
            return true;
        }
        return false;
    }

    public function setForbidden()
    {
        if (!$this->isAdmin()) {
            throw new ForbiddenHttpException('Вам не разрешено просматривать данную страницу');
        }
    }

    public function createUser($username, $email, $pass)
    {

        $this->username = $username;
        $this->email = $email;
        $this->date_reg = date("Y-m-d H:i:s", time());
        $this->avatar = '/img/default_avatar.jpg';
        $this->setPassword($pass);
        $this->generateAuthKey();
        $res = $this->save();


        if ($res) {
            $social = new UserSocial();
            $social->user_id = $this->id;
            $social->save();

            $user_doc = new UserDoc();
            $user_doc->user_id = $this->id;
            $user_doc->save();

            $user_partner_info = new UserPartnerInfo();
            $user_partner_info->user_id = $this->id;
            $user_partner_info->save();
        }

        return ($res) ? $this : NULL;
    }

    public function setInvationCodes() {
        $service = Servis::getInstance();
        $done = false;
        do {
            $this->invitation_code = $service->randomCode(10, true);
            $this->promo_code = strtoupper($service->randomCode(5));
            if(!static::find()->where('invitation_code = "' . $this->invitation_code . '" OR promo_code = "' . $this->promo_code . '"')->exists()) {
                $done = true;
            }
        } while(!$done);
        $this->save();
    }

    public function createUserFB($fb_id, $pass, $vk = false)
    {

        if ($vk) {
            $this->vk_id = $fb_id;
            $this->username = 'vk'.$fb_id;
        } else {
            $this->fb_id = $fb_id;
            $this->username = 'fb'.$fb_id;
        }
        $this->date_reg = date("Y-m-d H:i:s", time());
        $this->setPassword($pass);
        $this->generateAuthKey();

        $this->edit_username = 1;
        $res = $this->save();
        $this->sendPulseNewUserNotify();

        if ($res) {
            $social = new UserSocial();
            $social->user_id = $this->id;
            $social->user_id = $this->id;
            $social->save();

            $user_doc = new UserDoc();
            $user_doc->user_id = $this->id;
            $user_doc->save();

            $user_partner_info = new UserPartnerInfo();
            $user_partner_info->user_id = $this->id;
            $user_partner_info->save();
        }

        return ($res) ? $this : NULL;
    }

    public static function clearPhone($phone_str)
    {
        return preg_replace('/[^0-9]+/', '', $phone_str); //str_replace(array(' ','(',')','-','+'), '', $phone_str);
    }

    /**
     * breack date bithday to array day, mount, year
     */
    public function BreackDate()
    {

        if (!$this->date_bithday) {
            $this->date_bithday_arr = false;
        } else if ($this->date_bithday_arr === NULL) {
            $date = strtotime($this->date_bithday);
            $this->date_bithday_arr = [];
            $this->date_bithday_arr['day'] = date('d', $date);
            $this->date_bithday_arr['mount'] = date('m', $date);
            $this->date_bithday_arr['year'] = date('Y', $date);
        }

        return $this;
    }

    public static function getUsersList($thisUser = false)
    {
        return static::find()
            ->where(['<>', 'id', $thisUser])
            ->all();
    }


    public static function setVk_acaunt()
    {

        if (!empty($_POST)) {

            LogMy::getInstance()->setLog(['vk post' => $_POST, 'get' => $_GET], 'social');

            $str = $_POST['hash'];//'#access_token=8ded8404eb13c7d264ae006fc068e7fe9b02e6a016e3f32ba5b19296fb526fe3745ef591b893d1d99d4e0&expires_in=0&user_id=68554098&email=roma.06@mail.ru';
            parse_str($str, $res);

            if (!empty($res['email'])) {
                $user = User::findByEmail($res['email']);
                if (!empty($user)) {

                    $tmp_user = User::findByVK($res['user_id']);
                    if ($tmp_user) {
                        $tmp_user->vk_id = null;
                        $tmp_user->save();
                    }

                    $user->vk_id = $res['user_id'];
                    $user->save();
                    $res = Yii::$app->user->login($user, 3600 * 24 * 30);
                    if ($res) {
                        UserIpLog::setLog($user);
                        return ['success' => true, 'message' => ""];
                    }
                } else {
                    $tmp_user = User::findByVK($res['user_id']);
                    if ($tmp_user) {
                        $tmp_user->unlogin = false;
                        $tmp_user->save();
                        $res = Yii::$app->user->login($tmp_user, 3600 * 24 * 30);
                        if ($res) {
                            UserIpLog::setLog($user);
                            return ['success' => true, 'message' => ""];
                        }
                    } else {
                        $pass = rand();
                        $user = new User();
                        $user = $user->createUserFB($res['user_id'], $pass, true);
                        if ($user) {
                            $user->vk_id = $res['user_id'];
                            $user->username = substr(md5(uniqid(rand(), true)), 16);
                            $user->edit_username = 1;
                            $user->email = $res['email'];
                            $cookies = Yii::$app->request->cookies;
                            $cookie_partner = $cookies->getValue('partner');
                            if ($cookie_partner AND @$referal = User::findByUsername($cookie_partner)) {
                                $user->partner_id = $referal->id;
                                UserPartnerInfo::addPartner($user->getId(), $user->partner_id);
                            }

                            $user->save();
                            Yii::$app->user->login($user, 3600 * 24 * 30);
                            UserIpLog::setLog($user);
                            EmailTemplate::sendRegister($res['email'], $pass);
                            ChatTemplate::sendMessageFromTemplate($user->id, ChatTemplate::templateRegistration);

                            return ['success' => true, 'message' => ""];
                        } else {
                            return ['success' => false, 'message' => "delete after developer"];
                        }
                    }
                }
            } else if (isset($res['user_id'])) {
                $user = User::findByVK($res['user_id']);
                if (!empty($user)) {
                    $user->unlogin = false;
                    $user->save();
                    $res = Yii::$app->user->login($user, 3600 * 24 * 30);
                    if ($res) {
                        UserIpLog::setLog($user);
                        return ['success' => true, 'message' => ""];
                    }
                } else {
                    $pass = rand();
                    $user = new User();
                    $user = $user->createUserFB($res->user_id, $pass, true);
                    if ($user) {
                        $user->vk_id = $res['user_id'];
                        $user->username = substr(md5(uniqid(rand(), true)), 16);
                        $user->edit_username = 1;
                        $cookies = Yii::$app->request->cookies;
                        $cookie_partner = $cookies->getValue('partner');
                        if ($cookie_partner AND @$referal = User::findByUsername($cookie_partner)) {
                            $user->partner_id = $referal->id;
                            UserPartnerInfo::addPartner($user->getId(), $user->partner_id);
                        }
                        $user->save();
                        Yii::$app->user->login($user, 3600 * 24 * 30);
                        UserIpLog::setLog($user);
                        ChatTemplate::sendMessageFromTemplate($user->id, ChatTemplate::templateRegistration);
                        return ['success' => true, 'message' => ""];
                    } else {
                        return ['success' => false, 'message' => "delete after developer"];
                    }
                }

                return ['success' => false, 'message' => "регистрация нет email", 'url' => '/site/signup?fb=' . $_POST['id']];
            }
        }
    }

    /**
     * ajax login user in FB
     * @return array
     */
    public static function setFB_acaunt()
    {

        LogMy::getInstance()->setLog(['fb post' => $_POST, 'get' => $_GET], 'social');
        //если у пользователя нет имейла на ФБ
        if (empty($_POST['email'])) {
            $user = User::findByFB($_POST['id']);
            if (!empty($user)) {
                $user->fb_id = $_POST['id'];
                $user->unlogin = false;
                $user->save();
                $res = Yii::$app->user->login($user, 3600 * 24 * 30);
                if ($res) {
                    UserIpLog::setLog($user);
                    return ['success' => true, 'message' => ""];
                }
            } else {
                $pass = rand();
                $user = new User();
                $user = $user->createUserFB($_POST['id'], $pass);
                if ($user) {
                    $user->fb_id = $_POST['id'];
                    $cookies = Yii::$app->request->cookies;
                    $cookie_partner = $cookies->getValue('partner');
                    if ($cookie_partner AND @$referal = User::findByUsername($cookie_partner)) {
                        $user->partner_id = $referal->id;
                        UserPartnerInfo::addPartner($user->getId(), $user->partner_id);
                    }
                    $user->save();
                    Yii::$app->user->login($user, 3600 * 24 * 30);
                    UserIpLog::setLog($user);
                    ChatTemplate::sendMessageFromTemplate($user->id, ChatTemplate::templateRegistration);
                    return ['success' => true, 'message' => ""];
                } else {
                    return ['success' => false, 'message' => "delete after developer"];
                }
            }
        }
        $user = User::findByEmail($_POST['email']);
        if (!empty($user)) {

            $tmp_user = User::findByFB($_POST['id']);
            if ($tmp_user) {
                $tmp_user->fb_id = null;
                $tmp_user->save();
            }
            $user->unlogin = false;
            $user->fb_id = $_POST['id'];
            $user->save();
            $res = Yii::$app->user->login($user, 3600 * 24 * 30);
            if ($res) {
                UserIpLog::setLog($user);
                return ['success' => true, 'message' => ""];
            }
        } else {
            $tmp_user = User::findByFB($_POST['id']);
            if ($tmp_user) {
                $tmp_user->unlogin = false;
                $tmp_user->save();
                $res = Yii::$app->user->login($tmp_user, 3600 * 24 * 30);
                if ($res) {
                    UserIpLog::setLog($user);
                    return ['success' => true, 'message' => ""];
                }
            } else {
                $pass = rand();

                $user = new User();
                $user = $user->createUserFB($_POST['id'], $pass, false);
                if ($user) {

                    $user->fb_id = $_POST['id'];
                    $user->edit_username = 1;
                    $user->email = $_POST['email'];
                    $cookies = Yii::$app->request->cookies;
                    $cookie_partner = $cookies->getValue('partner');
                    if ($cookie_partner AND @$referal = User::findByUsername($cookie_partner)) {
                        $user->partner_id = $referal->id;
                        UserPartnerInfo::addPartner($user->getId(), $user->partner_id);
                    }
                    $user->save();
                    Yii::$app->user->login($user, 3600 * 24 * 30);
                    UserIpLog::setLog($user);
                    EmailTemplate::sendRegister($_POST['email'], $pass);
                    ChatTemplate::sendMessageFromTemplate($user->id, ChatTemplate::templateRegistration);

                    return ['success' => true, 'message' => ""];
                } else {
                    return ['success' => false, 'message' => "delete after developer"];
                }
            }
        }

        return ['success' => false, 'message' => "Что-то не так. Свяжитесь с администрацией"];
    }


    /**
     * начисление балов
     * @param $user_id
     * @param $summ
     * @param bool $up
     * @throws \yii\db\Exception
     */
    public static function BallinvestBalanceUp($user_id, $summ, $up = true)
    {
        if ($up) {
            return Yii::$app->db->createCommand("Update user set ball_invest = ball_invest + " . $summ . " where id = " . $user_id)->execute();
        } else {
            return Yii::$app->db->createCommand("Update user set ball_invest = ball_invest - " . $summ . " where id = " . $user_id)->execute();
        }
    }

    /**
     * увеличение бонусов
     * @param $user_id
     * @param $summ
     * @param bool $up
     */
    public static function BonuceBalanceUp($user_id, $summ, $up = true)
    {
        if ($up) {
            return Yii::$app->db->createCommand("Update user set balance_bonus = balance_bonus + " . $summ . " where id = " . $user_id)->execute();
        } else {
            return Yii::$app->db->createCommand("Update user set balance_bonus = balance_bonus - " . $summ . " where id = " . $user_id)->execute();
        }
    }

    /**
     * увеличение партнерского счета
     * @param $user_id
     * @param $summ
     * @param bool $up
     */
    public static function PartnerBalanceUp($user_id, $summ, $up = true)
    {
        $user = static::findIdentity($user_id);
        $user->balance_partner = $up ? $user->balance_partner + $summ : $user->balance_partner - $summ;
        return $user->save();
    }

    /**
     * удалит партнера у пользователей
     * @param $ids
     * @return bool
     */
    public static function delPartners($ids)
    {
        $ids = implode(',', $ids);
        $sql = 'Update user set partner_id = 0 where id IN (' . $ids . ')';
        Yii::$app->db->createCommand($sql)->execute();
        UserPartnerInfo::addIdsPartner();

        return true;
    }


    public static function getCountPartnersStatus($ids)
    {
        if (!$ids)
            return 0;

        $ids_user = implode(',', $ids);
        $sql = 'SELECT COUNT(`id`) id from `user` WHERE `id` IN (' . $ids_user . ') AND `status_in_partner` > 0';

//        var_dump(Yii::$app->db->createCommand($sql)->getRawSql()  );
        return Yii::$app->db->createCommand($sql)->queryScalar();
    }


    public static function usersArray()
    {
        $users = static::find()->where(['status' => [static::STATUS_ACTIVE, static::STATUS_ADMIN]])->all();
        $return = [];
        foreach ($users as $u) {
            $return[$u->id] = $u->username . '(id:' . $u->id . ')   ' . $u->firstname . ' ' . $u->lastname;
        }
        return $return;
    }

    public function generateLetterAvatar($force_update = false)
    {
        if (isset($this->avatar) AND !$force_update) {
            return true;
        }

        $string = '';
        if (isset($this->firstname) AND isset($this->lastname) AND !empty($this->firstname) AND !empty($this->lastname)) {
            $string = mb_substr($this->firstname, 0, 1, "UTF-8") . ' ' . mb_substr($this->lastname, 0, 1, "UTF-8");
        } elseif (isset($this->firstname) AND strlen($this->firstname) > 1) {
            $pieces = explode(" ", $this->firstname);
            if (count($pieces) > 1) {
                $string = mb_substr($pieces[0], 0, 1, "UTF-8") . ' ' . mb_substr($pieces[1], 1, 1, "UTF-8");
            } else {
                $string = mb_substr($this->firstname, 0, 1, "UTF-8") . ' ' . mb_substr($this->firstname, 1, 1, "UTF-8");
            }
        } elseif (isset($this->lastname) AND strlen($this->lastname) > 1) {
            $string = mb_substr($this->lastname, 0, 1, "UTF-8") . ' ' . mb_substr($this->lastname, 1, 1, "UTF-8");
        } elseif (isset($this->username) AND strlen($this->username) > 1) {
            $string = mb_substr($this->username, 0, 1, "UTF-8") . ' ' . mb_substr($this->username, 1, 1, "UTF-8");
        } else {
            $this->avatar = '/img/avatar-placeholder.png';
            $this->save();
            return true;
        }


        $newPath = static::realizePath();
        $name = Servis::getInstance()->randomCode(12);
        $avatar = new LetterAvatar($string, 'circle', 300);
        $path = dirname(dirname(__DIR__)) . '/frontend/web/upload/avatars/' . $newPath . $name;
        $avatar->saveAs($path . '.jpeg', "image/jpeg");
        $this->avatar = '/upload/avatars/' . $newPath . $name . '.jpeg';
        $this->save();
    }

    static function realizePath()
    {
        $pathToUpload = dirname(dirname(__DIR__)) . '/frontend/web/upload/';
        if (!is_dir($pathToUpload)) {
            mkdir($pathToUpload, 0755);
        }
        $pathToUpload = dirname(dirname(__DIR__)) . '/frontend/web/upload/avatars/';
        if (!is_dir($pathToUpload)) {
            mkdir($pathToUpload, 0755);
        }
        $path1 = Servis::getInstance()->randomCode();
        $path2 = Servis::getInstance()->randomCode();
        if (!is_dir($pathToUpload . $path1)) {
            mkdir($pathToUpload . $path1, 0755);
        }
        if (!is_dir($pathToUpload . $path1 . '/' . $path2)) {
            mkdir($pathToUpload . $path1 . '/' . $path2, 0755);
        }
        return $path1 . '/' . $path2 . '/';
    }


    public function getActiveBonuses()
    {
        $bonuses = BalanceBonusLog::getUserActiveBonuses($this->id);
        return $bonuses;
    }

    public function getSummBonuses()
    {
        $bonuses = BalanceBonusLog::find()->where(['user_id' => $this->id, 'expired' => 0])->sum('summ_now');
        return $bonuses ? $bonuses : 0;
    }

    public function getManagerCard()
    {
        return ManagerCard::getUserManagerCard($this->id);
    }


    public static function findUsers($search)
    {
        $status = [static::STATUS_ACTIVE];
        return static::find()
            ->where(['OR', 'username LIKE "%' . $search . '%"', 'firstname LIKE "%' . $search . '%"', 'lastname LIKE "%' . $search . '%"', 'id LIKE "%' . $search . '%"'])
            ->andWhere(['status' => $status])
            ->limit(50)
            ->all();
    }

    public static function findUsersForList($search)
    {
        if (!$users = static::findUsers($search)) {
            return false;
        }
        $data = [];
        foreach ($users as $u) {
            $data[] = $u->getUserForList();
        }
        return $data;
    }

    public function getNameString($middlename = false)
    {
        $string = '';
        if (isset($this->firstname)) {
            $string = $this->firstname;
        }
        if ($middlename) {
            $string = empty($string) ? $this->middlename : $string . ' ' . $this->middlename;
        }
        if (isset($this->lastname)) {
            $string = empty($string) ? $this->lastname : $string . ' ' . $this->lastname;
        }

        if (empty($string)) {
            $string = $this->username;
        }
        return $string;
    }


    public static function findUsersForMessages($search)
    {
        if (!$users = static::findUsers($search)) {
            return false;
        }
        $protocol = stripos($_SERVER['SERVER_PROTOCOL'], 'https') === true ? 'https://' : 'http://';
        $data = [];
        foreach ($users as $u) {
            $unread = $u->unread_messages;
            $chatName = $u->firstname . ' ' . $u->lastname;
            $u->username = (trim($u->username) != '') ? $u->username : 'никнейм не задан';
            $chatName = (trim($chatName) == '') ? $u->username : $chatName;
            $data[] = [
                'id' => $u->id,
                'avatar' => $protocol . Yii::$app->params['frontendDomen'] . $u->avatar,
                'name' => $chatName,
                'unread' => $unread ? $unread : '',
            ];
        }
        return $data;
    }

    public function getUserForList()
    {
        $string = 'id:' . $this->id;
        $string .= $this->username ? ' ' . $this->username : null;
        $name = '';
        $name .= $this->firstname ? $this->firstname : null;
        if ($this->lastname) {
            $name .= $name != '' ? ' ' . $this->lastname : $this->lastname;
        }
        $string .= $name != '' ? ' (' . $name . ')' : null;
        $data['id'] = $this->id;
        $data['string'] = $string;
        return $data;
    }


    public function hasMinDeposit()
    {
        if ($this->countInvested() >= 10) {
            return true;
        }
        return false;
    }
    
    
    public function verificate()
    {
        if (!$this->verified) {
            $this->verified = true;
        }

        $documents = UserDoc::findIdentityUserId($this->id);
        if ($documents->need_verification) {
            $documents->need_verification = false;
            $documents->save();
        }
        ChatTemplate::sendMessageFromTemplate($this->id, ChatTemplate::successVerification, []);
        $this->save();
      //  BonusDebt::payOutUserDebts($this->id);
        return true;
    }


    public function giveFirstBonus()
    {
        return false;
        if ($this->first_bonus_recived) {
            return false;
        }

        $balance_bonus = new BalanceBonusLog();
        $balance_bonus->add(50, $this->getId(), 7, 'За первое пополнение');

        $this->first_bonus_recived = true;
        $this->save();
    }

    public function sendPulseNewUserNotify()
    {
        SendPulseNotification::send($this->id, SendPulseNotification::registration,[
            'email' => $this->email,
            'phone' => $this->phone,
            'name' => $this->firstname ? $this->firstname : $this->username,
        ]);
    }

    public function getActiveObjective()
    {
        if (!$objective = UserObjectives::find()->where(['user_id' => $this->id, 'date_end' => null ])->one()) {
//            if (!$objective = UserObjectives::find()->where(['user_id' => $this->id])->orderBy('date_end DESC')->one()) {
                return false;
//            }
        }
        $balance = $this->getBalance();
        if($balance < 0.01) {
            $objective->percent = 0;
        } else {
            $objective->percent = Servis::getInstance()->beautyDecimal($balance / ($objective->sum_end / 100));
        }
        $objective->percent = $objective->percent > 100 ? 100 : $objective->percent;
        $objective->getData();

        if($objective->percent == 100) {
            UserObjectives::updateAll(['date_end' =>  date('Y-m-d H:i:s')], ['id' => $objective->id]);
      //      $objective->months_text = Yii::t('app', 'Готово') . '!';
            $objective->refresh();
        } else {
            $objective->days_to = intval(log($objective->sum_end / $balance, 1.01));
        }
        return $objective;
    }

    public function getBalance()
    {
        return static::find()
            ->where(['id' => $this->id])
            ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status = 4 AND summ > 0 AND user_id = '. $this->id .') as bl1 on bl1.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm2, user_id FROM balance_log WHERE (status = 4 OR (operation = 1 AND status = 0)) AND summ < 0 AND user_id = '. $this->id . ') as bl2 on bl2.user_id = user.id')
            ->sum('user.balance + if(sm1 IS NULL, 0, sm1) - if(sm2 IS NULL, 0, sm2) ');
    }

    public function getProfit($current_profit = 0)
    {
        return static::find()
            ->where(['id' => $this->id])
            ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status = 4 AND summ > 0 AND operation <> 0 AND user_id = '. $this->id.') as bl1 on bl1.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm2, user_id FROM balance_log WHERE (status = 4 OR (operation = 1 AND status IN (0,1,3))) AND summ < 0 AND user_id = '. $this->id.') as bl2 on bl2.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm3, user_id FROM balance_log WHERE status = 1 AND operation IN (0,3) AND user_id = '. $this->id.') as bl3 on bl3.user_id = user.id')
            ->sum('user.balance + if(sm1 IS NULL, 0, sm1) - if(sm2 IS NULL, 0, sm2) - if(sm3 IS NULL, 0, sm3)') ;
        //+ $this->balance $current_using_summ;
    }


    public function getInvitedFounds()
    {
        return static::find()
                ->where(['partner_id' => $this->id])
                ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status = 4 AND summ > 0 GROUP BY user_id) as bl1 on bl1.user_id = user.id')
                ->leftJoin('(SELECT SUM(summ) as sm2, user_id FROM balance_log WHERE (status = 4 OR (operation = 1 AND status = 0)) AND summ < 0 GROUP BY user_id) as bl2 on bl2.user_id = user.id')
                ->sum('user.balance + if(sm1 IS NULL, 0, sm1) - if(sm2 IS NULL, 0, sm2) ');
        
    }


    public static function setEventsNotice($message = false){
        $events_notice = $message ? true : false;
        $events_complete = false;
        $h = date('H');
        if($h >= 10 AND $h < 15) {
            return static::updateAll(['events_notice' => $events_notice, 'events_complete' => false], 'id IS NOT NULL');
        }
        $log = DaysLog::getLog();
        if($events = Events::find()->where(['days_log_id' => $log->id])->all()) {
            foreach ($events as $e) {
                $events_notice = true;
                if($e->result) {
                    $events_complete = true;
                    break;
                }
            }
        }
        return static::updateAll(['events_notice' => $events_notice, 'events_complete' => $events_complete], 'id IS NOT NULL');
    }

    public function can($role) {
        return Yii::$app->authManager->checkAccess($this->id, $role);
    }

    public function countUnreadMessages() {
        $cnt = UserMessage::find()->where(['date_delete' => null, 'user_id' => $this->id, 'status' => 0])->count();
        return $cnt ?? 0;
    }
}
