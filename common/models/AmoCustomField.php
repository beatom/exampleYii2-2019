<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $amo_field_id
 * @property string $name
 * @property integer $sort
 * @property integer $field_type
 * @property boolean $is_system
 * @property boolean $is_editable
 * @property boolean $is_required
 * @property boolean $is_multiple
 * @property boolean $is_deletable
 * @property boolean $is_visible
 * @property string $params
 */
class AmoCustomField extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'amo_custom_field';
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

    


}
