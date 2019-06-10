<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use common\models\SmsTemplate;

/**
 * Signup form
 */
class SmsForm extends Model
{
    public $id;
    public $text;
    public $synonym;
    public $comment;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['synonym', 'text'], 'required'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function add( $add = true, $sms_template=null )
    {
        if (!$this->validate()) {
            return null;
        }

        if( $add ){
            $sms_template = new SmsTemplate();
        }

        $sms_template->text = $this->text;
        $sms_template->synonym = $this->synonym;
        $sms_template->comment = $this->comment;

        return $sms_template->save();
    }

    public function setData($sms_template){
        $this->text = $sms_template->text;
        $this->synonym = $sms_template->synonym;
        $this->comment = $sms_template->comment;
    }
}
