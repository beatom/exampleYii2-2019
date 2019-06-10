<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $facebook
 * @property string $vk
 * @property string $instagram
 * @property string $skype
 * @property string $whatsapp
 * @property string $twitter
 * @property string $telegram
 */
class UserSocial extends ActiveRecord
{

    public static $arr_social = ['facebook','vk','instagram','skype','whatsapp','twitter', 'telegram'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_social';
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
    public static function findIdentityUserId($id)
    {
        $user_social = static::findOne(['user_id' => $id]);
        if(!$user_social) {
            $user_social = new static();
            $user_social->user_id = $id;
            $user_social->save();
        }
        return $user_social;
    }


    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getArray() {
        $return_arr = [];
        foreach ($this as $key => $value) {
            if(!in_array($key,static::$arr_social) OR !$value) {
                continue;
            }
            $return_arr[$key] = $value;
        }
        return $return_arr;
    }


}
