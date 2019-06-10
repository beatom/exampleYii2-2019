<?php

namespace common\models\promo;

use yii\db\ActiveRecord;
use Yii;
use common\models\promo\PromoBannerImage;
use yii\helpers\Url;
/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property date $date_add
 * @property boolean $show
 * @property string $folder
 */
class PromoBanner extends ActiveRecord
{

    public $main_image = '';
    public $sizes = [];
    public $types = [];
 //   public $info = [];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promo_banner';
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
            [['show', 'name'], 'safe'],
            [['name', 'folder'], 'required'],
            [['name', 'folder'], 'string', 'min' => 1, 'max' => 255],
            [['show'], 'boolean', 'trueValue' => '1', 'falseValue' => '0'],
            [['show'], 'default', 'value' => '0'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function getImages()
    {
        return $this->hasMany(PromoBannerImage::class, ['promo_banner_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function setMainImage()
    {
        $image = PromoBannerImage::find()->where(['promo_banner_id' => $this->id, 'size' => '200x400', 'is_main' => 1])->one();
        if(!$image) {
            $image = PromoBannerImage::find()->where(['promo_banner_id' => $this->id])->one();
        }
        $this->main_image = $image->link;
//        $this->types = PromoBanner::getTypes($this->id);
//        $this->sizes = PromoBanner::getSizes($this->id);
      //  $this->info = PromoBanner::getMaterials($this->id);
        return $this->main_image;
    }

    public static function getMaterials($id)
    {
        $base_url = Url::base(true);
        $partner_link = $base_url.'/?partner='.Yii::$app->user->identity->username;
        $info = [];
        if(!$banner = static::findIdentity($id)) {
            return $info;
        }

        $images = PromoBannerImage::find()->where(['promo_banner_id' => $id])->all();
        foreach ($images as $image) {
            $img_array = [];
            $img_array['type'] = $image->type;
            $img_array['size'] = $image->size;
            $img_array['download'] = $image->archive_link ?  $image->archive_link : $image->link;
            $img_array['partner_link'] = $image->type == 'html' ? ' <iframe src="'.$base_url.$image->link.'?link1='.$partner_link.'" '.$image->html_size.'></iframe> ' : "<a href='$partner_link' title='invest24' target='_blank'><img src='$base_url$image->link' alt='invest24'></a>";
            $img_array['preview'] = $img_array['partner_link'];
            $img_array['partner_link'] = htmlentities($img_array['partner_link']);
            if(!isset($info[$image->size])) {
                $info[$image->size] = [ strtoupper($image->type) => $img_array];
            } else {
                $info[$image->size][strtoupper($image->type)] = $img_array;
            }
        }
        $info['sizes'] = static::getSizes($id);
        $info['types'] = static::getTypes($id);
        return $info;
    }


    public static function getMaterialsNew()
    {
        $base_url = Url::base(true);
        $partner_link = $base_url.'/'.Yii::$app->user->identity->invitation_code;

        $data = [
            'png' => [], 'gif' => [], 'html' => []
        ];
        foreach (PromoBannerImage::find()->orderBy('type DESC,size ASC')->all() as $image) {
            $img_array = [];
            $img_array['type'] = $image->type;
            $img_array['size'] = $image->size;
            $img_array['download'] = $image->archive_link ?  $image->archive_link : $image->link;
            $img_array['partner_link'] = $image->type == 'html' ? ' <iframe src="'.$base_url.$image->link.'?link1='.$partner_link.'" '.$image->html_size.'></iframe> ' : "<a href='$partner_link' title='invest' target='_blank'><img src='$base_url$image->link' alt='invest'></a>";
            $img_array['preview'] = $img_array['partner_link'];
            $img_array['partner_link'] = htmlentities($img_array['partner_link']);
            $img_array['html_size'] = htmlentities($image->html_size);
            $data[$image->type][$image->size][] = $img_array;

        }
        return $data;
    }
    

    public static function getSizes($id = false, $show = 1)
    {
        return static::getInfo('size', $id, $show);
    }

    public static function getTypes($id = false, $show = 1)
    {
        return static::getInfo('type', $id, $show);
    }

    public function getInfo($target = 'type', $id = false, $show = 1) {
        $return = [];
        $where = [];
        if ($show) {
            $where['show'] = 1;
        }
        if ($id) {
            $where['promo_banner.id'] = $id;
        }
        $query = static::find()
            ->select('distinct(promo_banner_image.'.$target.') as param')
            ->leftJoin('promo_banner_image', 'promo_banner_image.promo_banner_id = promo_banner.id')
            ->asArray();
        if(!empty($where)) {
            $query->where($where);
        }
        $params = $query->all();

        foreach ($params as $param) {
            if (isset($param['param'])) {
                $return[] = $target == 'type' ? strtoupper($param['param']) : $param['param'];
            }
        }
        return $return;
    }

}
