<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use common\models\LogConfirm;
use Yii;
use common\models\EmailTemplate;
use common\models\UserPartnerInfo;

/**
 * Signup form
 */
class MessageUserForm extends Model
{
    public $firstname;
    public $avatar;
    public $canReciveAnswer;



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['firstname', 'trim'],
            ['firstname', 'required', 'message'=>'Необходимо заполнить'],
            ['firstname', 'string', 'min' => 2, 'max' => 255],

            ['avatar', 'trim'],
            ['avatar', 'string', 'max' => 255],
            ['avatar', 'safe'],

            ['canReciveAnswer', 'boolean', 'trueValue' => true, 'falseValue' => false],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function creatSender()
    {
        if (!$this->validate()) {
            return null;
        }

        $hashstring = md5($this->firstname . date('Y-m-d H:i:s'));
        $hash = $hashstring . '_' . str_replace(' ', '', $this->firstname);

        $user = new User();
        $res = $user->createUser($hash, $hash.'@invest24.com', $hashstring);
        if($res){
            $user->firstname = $this->firstname;
            $user->status = ($this->canReciveAnswer) ? User::STATUS_MESSAGE_SENDER_RECIVER : User::STATUS_MESSAGE_SENDER;
            $user->save();

            UserPartnerInfo::addIdsPartner();

            return $user->id;
        }

        return false;
    }
}
