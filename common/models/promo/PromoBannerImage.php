<?php

namespace common\models\promo;

use yii\db\ActiveRecord;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property integer $promo_banner_id
 * @property string $link
 * @property date $date_add
 * @property string $type
 * @property string $size
 * @property string $html_size
 * @property boolean $is_main
 * @property string $archive_link
 */
class PromoBannerImage extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promo_banner_image';
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
