<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $pasport_1
 * @property string $pasport_2
 * @property date $date_add
 * @property boolean need_verification
 *
 */
class UserDoc extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_document';
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

    public static function countNewDocuments()
    {
        return static::find()->where('need_verification = 1')->count();
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

}
