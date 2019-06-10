<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;
use common\models\EmailTemplate;
/**
 * Signup form
 */
class EmailForm extends Model
{
    public $id;
    public $title;
    public $text;
    public $synonym;
    public $comment;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['synonym', 'text', 'title'], 'required'],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function add( $add = true, $email_template=null )
    {
        if (!$this->validate()) {
            return null;
        }

        if( $add ){
            $email_template = new EmailTemplate();
        }

        $email_template->text = $this->text;
        $email_template->title = $this->title;
        $email_template->synonym = $this->synonym;

        return $email_template->save();
    }

    public function setData($email_template){
        $this->text = $email_template->text;
        $this->title = $email_template->title;
        $this->synonym = $email_template->synonym;
        $this->comment = $email_template->comment;
    }
}
