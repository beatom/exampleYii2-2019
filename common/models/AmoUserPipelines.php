<?php
namespace common\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $synergy_1
 * @property string $meet_up_moscow
 * @property string $save_capital
 * @property string $save_capital_vebinar
 * @property string $meaningful_customer_card
 * @property string $plus_50
 * @property string $loyalty_program
 * @property string $trading_school
 * @property string $mailing_material
 * @property string $vebinar_seminar
 */
class AmoUserPipelines extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amo_user_pipelines';
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
    
    public static function findIdentityUserId($user_id)
    {
        return static::findOne(['user_id' => $user_id]);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getOrCreate($user_id) {
        if(!$result = static::findIdentityUserId($user_id)) {
            $result = new static();
            $result->user_id = $user_id;
            $result->save();
        }
        return $result;
    }
    


}
