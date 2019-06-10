<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * User model
 *
 * @property integer $id
 * @property string $text
 * @property string $synonym
 *
 */
class SmsTemplate extends ActiveRecord
{
    const templateRegistrationConfirm = 1;
  //  const templateWithdrawFounds = 2;
   // const templateTransferToUser = 3;
    const templatePhoneConfirm = 4;
    const templateChangePassword = 5;
  //  const templateDisableSmsNotification = 6;
  //  const templateEndedBonusesIncome = 7;
    const templateChangePaymentSystem = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_template';
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


    public static function setMessage($templateId, $data = [])
    {
        $template = static::findIdentity($templateId);
        $placeholders['{{site_name}}'] = 'invest.biz';
        foreach ($data as $key => $value) {
            $placeholders['{{' . $key . '}}'] = $value;
        }
        return str_replace(array_keys($placeholders), array_values($placeholders), $template->text);
    }

}
