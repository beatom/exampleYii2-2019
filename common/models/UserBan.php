<?php
namespace common\models;

use common\models\User;
use yii\db\ActiveRecord;
use Yii;


/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $manager_id
 * @property date $date_add
 * @property date $date_end
 * @property boolean $active
 * @property string $comment
 */
class UserBan extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ban';
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

    public function attributeLabels()
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


    public static function banUser($user_id, $manager_id, $date_end = null, $comment = null) {
        if($ban = static::getUserStatus($user_id)){
            return $ban;
        }
        $ban = new static();
        $ban->user_id = $user_id;
        $ban->manager_id = $manager_id;
        $ban->date_end = $date_end ? $date_end : date('2099-01-01 23:59:59');
        $ban->comment = $comment;
        $ban->save();
        return $ban;
    }

    public static function getUserStatus($user_id) {
        return static::find()->where(['user_id' => $user_id, 'active' => 1])->andWhere('date_add < NOW() AND NOW() < date_end')->one();
    }
    


}
