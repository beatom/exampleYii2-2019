<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use common\models\User;

/**
 * User model
 *
 * @property integer $id
 * @property string $phone
 * @property string $comment
 * @property date $date_end
 * @property boolean $type
 * @property boolean $active
 */
class SmsBlock extends ActiveRecord
{
    const checking_time = 30;
    const minuts_for_block = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_block';
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


    public static function addBlock($number, $comment = null)
    {
        if (static::find()->where(['phone' => User::clearPhone($number), 'type' => 1, 'active' => 1])->exists()) {
            return \Yii::t('app', 'Ваш номер заблокирован для отправки смс. Для решения проблемы свяжитесь с администрацией.');
        }
        $type = 0;
        if (static::find()->where(['phone' => User::clearPhone($number), 'type' => 0])->exists()) {
            $type = 1;
        }
        $message = $type ?  \Yii::t('app', 'Ваш номер заблокирован для отправки смс. Для решения проблемы свяжитесь с администрацией.') : \Yii::t('app', 'Превышено количество запрашиваемых кодов на один номер. Попобуйте через') . ' ' . static::minuts_for_block . ' '.\Yii::t('app', 'минут');
        $block = new static();
        $block->phone = User::clearPhone($number);
        $block->comment = $comment;
        $block->type = $type;
        $block->date_end = $type ? null : date('Y-m-d H:i:s', strtotime(" +" . static::minuts_for_block . ' minutes'));
        return $block->save() ? $message : false;

    }

    public static function isBlocked($number)
    {
        if (static::find()->where(['phone' => User::clearPhone($number), 'type' => 1, 'active' => 1])->exists()) {
            return \Yii::t('app', 'Ваш номер заблокирован для отправки смс. Для решения проблемы свяжитесь с администрацией.');
        }
        if (static::find()->where(['phone' => User::clearPhone($number), 'type' => 0])->andWhere('date_end > "' . date('Y-m-d H:i:s') . '"')->exists()) {
            return \Yii::t('app', 'Превышено количество запрашиваемых кодов на один номер. Попобуйте через') . ' ' . static::minuts_for_block . ' '.\Yii::t('app', 'минут');
        }
        return false;
    }


}
