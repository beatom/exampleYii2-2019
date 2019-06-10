<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property date $date_add
 * @property string $title
 * @property string $description
 * @property string $img
 * @property string $video_link
 * @property string $video_duration
 * @property boolean $show
 */
class WebinarArchive extends ActiveRecord
{


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'webinar_archive';
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

    public static function getPosts($offset = 0, $limit = 8){
        $data['posts'] = static::find()
            ->select(['title', 'description', 'img', 'video_link', 'video_duration'])
            ->where(['show' => 1])
            ->orderBy('date_add DESC')
            ->offset($offset)
            ->limit($limit)
            //  ->limit( $limit )
            ->asArray()
            ->all();
        $data['has_more'] = static::find()->where(['show' => 1])->count() > (count($data['posts']) + $offset) ? true : false;
        return $data;
    }

}
