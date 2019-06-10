<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property string $img
 * @property string $img_en
 * @property string $title
 * @property string $text_1
 * @property string $text_2
 * @property string $subtitle
 * @property string $text_button
 * @property string $url
 * @property integer $position
 * @property integer $status
 *
 */
class Banner extends ActiveRecord
{

    public static $arr_positions = [
        1 => 'Слайды в кабинете',
        2 => 'На главной',
        3 => 'Баннеры в кабинете',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'banner';
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

    public static function getActiveBaners( $pos ){
        return static::find()->where('position = '.$pos.' AND status = 1')->all();
    }

}
