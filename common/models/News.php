<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $date_add
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
class News extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
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

    public static function getActiveNews( $limit = 3 ){
        return static::find()
            ->orderBy('date_add DESC')
	        ->where('status = 1')
            ->limit( $limit )
            ->all();
    }

    public static function getUniqueFields() {
        $result = [
            'from' => [],
            'from_en' => [],
            'cat' => [],
            'cat_en' => []
        ];
        $fields = static::find()->select(['cat', 'cat_en', '`from`', 'from_en'])->groupBy(['cat', 'cat_en', '`from`', 'from_en'])->all();

        foreach ($fields as $f) {
            foreach ($f->attributes as $key => $a) {
                if(!$a) {
                    continue;
                }
                if(!in_array($a, $result[$key])) {
                    $result[$key][] = $a;
                }
            }
        }
        return $result;
    }

    public function markAsRead() {
        NewsUserRed::readNews($this->id);
    }
}
