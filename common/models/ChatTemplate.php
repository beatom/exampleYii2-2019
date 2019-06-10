<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * User model
 *
 * @property integer $id
 * @property string $text
 * @property string $title
 */
class ChatTemplate extends ActiveRecord
{
    const templateRegistration = 1;
    const templateWithdrawApprove = 2;
    const templateWithdrawDenial = 3;
    const templateTransferApprove = 4;
    const templateTransferDenial = 5;
    const templatePartnerStatus = 6;
    const templateCapitalProtection = 7;
    const templateBonuses = 8;
    const overdraftClose = 9;
    const overdraftOpen = 10;
    const overdraftTimeWeek = 11;
    const bonusDebtInsert = 12;
    const bonusDebtClose = 13;
    const successVerification = 14;
    const failVerification = 15;
    const bonus7success = 16;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_template';
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
            ['sender_id', 'integer'],
            ['sender_id', 'required', 'message' => 'Выберите отправителя'],
            [['text', 'synonym', 'title'], 'trim'],
            [['text', 'synonym', 'title'], 'required', 'message' => 'Необходимо заполнить'],
            [['text', 'synonym', 'title'], 'string'],
           
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getSender()
    {
        return $this->hasOne(Sender::class, ['id' => 'sender_id']);
    }

    public static function checkSenders()
    {
        if (!empty(static::find()->where(['sender_id' => null])->all())) {
            Yii::$app->session->setFlash('warning', 'У вас не настроены один или несколько отправителей в шаблонах сообщений <a href="' . Url::base(true) . '/messages/templates">Настроить</a>');
        }
    }

    public static function sendMessageFromTemplate($anotherUserId, $template, $data = array())
    {
        $tmpl = static::findIdentity($template);
        if (!$tmpl OR $tmpl->sender_id == null) {
            return false;
        }
        $placeholders['{{site_name}}'] = Yii::$app->params['siteNameFull'];
        foreach ($data as $key => $value) {
            $placeholders['{{' . $key . '}}'] = $value;
        }

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $tmpl->text);

        UserMessage::sendMessage($anotherUserId, $tmpl->sender_id, $message, $tmpl->title);
        return true;
    }

}
