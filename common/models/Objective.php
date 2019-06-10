<?php
namespace common\models;

use yii\db\ActiveRecord;


/**
 * User model
 *
 * @property integer $id
 * @property integer $max_sum
 *
 */
class Objective extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'objective';
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
            ['max_sum','required'],
            ['max_sum','integer', 'min' => 0, 'message' => 'Значение должно быть целым числом'],
            ['max_sum', 'unique', 'message' => 'Цель с такой максимальной суммой уже существует'],
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
