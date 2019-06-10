<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;
use common\models\QueueMail;

/**
 * User model
 *
 * @property integer $id
 * @property string $text
 * @property string $title
 * @property string $synonym
 *
 */
class EmailTemplate extends ActiveRecord
{
    const REGISTER_USER = 1;
    const NOTIFY_MESSAGES = 2;
    const NOTIFY_MESSAGES_FROM_USER = 3;
    const CREATE_REAL_TRADE_ACCOUNT = 4;
    const CREATE_DEMO_TRADE_ACCOUNT = 5;
    const EMAIL_CONFIRM = 6;
    const TRANSACTION_USER_USER = 7;
    const receiving_transfer = 8;
    const approval_transfer  = 9;
    const CREATE_DU_ACCOUNT = 10;
    const BALANCE_BONUS_UP = 11;



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'email_tempalte';
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

    /**
     * получит шаблон для письма
     * @param $data
     * @return array
     */
    public function getEmailTemplate(  $data ){
        foreach ( $data as $key => $value) {
//            if($key == 'link') {
//                $placeholders['{{' . $key . '}}'] = '<a href="'.$value.'" target="_blank">'.$value.'</a>';
//            } else {
//
//            }

            $placeholders['{{' . $key . '}}'] = $value;

        }
        $html_text = str_replace(array_keys($placeholders), array_values($placeholders), $this->text);
        $html_title = str_replace(array_keys($placeholders), array_values($placeholders), $this->title);


        $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset="utf-8" /><title>';

        $html .= $html_title;
        $html .= '</title></head><body>';
        $html .= '<h1>'.$html_title.'</h1>';
        $html .= $html_text;
        $html .= '</body></html>';

        $text = strip_tags( $html_title ).'
';
        $text .= trim(strip_tags( $html_text ));

        return [
            'html' => $html,
            'text' => $text,
            'title'=> $html_title
        ];

    }

    /**
     * @param $to
     * @param $subject
     * @param array $body html|text
     * @param string $from
     */
    public function sendMail( $to, $subject, $body, $from='info@invest24.com'){
        return true;
        if($to) {
            Yii::$app
                ->mailer
                ->compose()
                ->setFrom($from)
                ->setTo($to)
                ->setSubject($subject)
                ->setTextBody($body['text'])
                ->setHtmlBody($body['html'])
                ->send();
        }
    }

    public static function sendRegister($email, $pass, $name='Сможете изменить в личном кабинете',$partner=''){
        return true;
        //send email
        $data = [];
        $data['user_name'] = $name;
        $data['password'] = $pass;
        $data['partner'] = $partner;

        QueueMail::addTask(
            Yii::$app->params['supportEmail'],
            $email,
            '',
            EmailTemplate::REGISTER_USER,
            $data );
    }

}
