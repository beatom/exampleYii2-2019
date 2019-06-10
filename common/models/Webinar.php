<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property date $date_end
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 * @property string $img
 * @property string $img_en
 * @property string $button_text
 * @property string $button_text_en
 * @property string $button_link
 * @property boolean $show
 * @property boolean $show_as_active
 */
class Webinar extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webinar';
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

    public static function getUpcomingEvents(){
        return static::find()
            ->where('`show` = 1 AND (`date_end` > "'.date('Y-m-d H:i:s').'" OR show_as_active = 1 )')
            ->orderBy('date_end ASC')
          //  ->limit( $limit )
              ->asArray()
            ->all();
    }

}
