<?php
namespace frontend\models\user;

use common\models\UserSocial;
use yii\base\Model;
use common\models\User;
use Yii;
use common\models\SmsManager;
use common\models\LogConfirm;
use common\models\SmsTemplate;

/**
 * Signup form
 */
class SecurityForm extends Model
{
    public $oldpass;
    public $newpass1;
    public $newpass2;
    public $sms_code;
    public $step = 1;
    public $message = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sms_code', 'newpass1', 'newpass2', 'oldpass', 'message', 'step'], 'safe'],
            [['sms_code', 'newpass1', 'newpass2', 'oldpass'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'newpass1' => 'Новый пароль',
            'newpass2' => 'Подтверждение пароля',
            'oldpass' => 'Старый пароль ',
            'sms_code' => 'Код из смс',

        ];
    }

    /**
     * @param $user
     * @return bool|null
     */
    public function save($user)
    {
        if (!$this->validate()) {
            return null;
        }

        if (!empty($this->newpass1)) {
            if (!$user->validatePassword($this->oldpass)) {
                $this->addError('oldpass', 'Не верный пароль');
                return null;
            }
            if ($this->newpass1 == $this->newpass2) {
                $user->setPassword($this->newpass1);
            } else {
                $this->addError('newpass2', 'Пароли не совпадают');
                return null;
            }
        }

        if (SmsManager::getActiveSmsProvider()) {
            if ($this->step == 1) {
                if ($mess = SmsManager::stopSpam($user->phone, 'Подтверждение телефона в настройках ЛК')) {
                    $this->addError('newpass2', $mess);
                    return null;
                }

                $phone = User::clearPhone($user->phone);
                $code = rand(10000, 99999);
                $confirm = new LogConfirm();
                $confirm->date_add = time();
                $confirm->phone = $phone;
                $confirm->code = $code;
                $confirm->save();
                SmsManager::sendOne(SmsTemplate::templateChangePassword, $phone, ['code' => $code], $user->id);
                $this->step = 2;
                return null;
            } else {
                $model = new SmsConfirmForm();
                $model->sms_code = $this->sms_code;
                if (!$model->checkCode($user, 'sms_confirm')) {
                    $this->addError('sms_code', 'Смс код неверный');
                    return null;
                }
            }
        }

        return $user->save();
    }


}
