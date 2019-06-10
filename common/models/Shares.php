<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_start
 * @property date $date_end
 * @property string $img
 * @property string $title
 * @property string $text_small
 * @property string $text_big
 * @property string $synonym
 * @property string $status
 * @property string $meta_description
 * @property string $meta_title
 * @property string $meta_keyword
 *
 */
class Shares extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'shares';
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
        return static::findOne(['user_id' => $id]);
    }
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getActiveShares(){
        $now = date("Y-m-d");

        return static::find()
            ->where('date_end >= "'.$now.'" AND status = 1')
            ->orderBy('date_end DESC')
            ->all();
    }

}
