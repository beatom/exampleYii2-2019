<?php
namespace common\models;

use common\service\Servis;
use Yii;
use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $comment
 * @property float $sum_start
 * @property integer $sum_end
 * @property date $date_add
 * @property date $date_end
 * @property string $image
 * @property double $percent
 *
 */
class UserObjectives extends ActiveRecord
{

    public $percent = 0;
    public $data = null;
    public $image_file = null;
    public $days_to = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_objectives';
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
        return $this->getRules();
    }

    public function getRules() {
        $min_sum = Yii::$app->user->identity->getBalance() + 1;
        $top_objctive = Objective::find()->orderBy('max_sum DESC')->one();
        $max_sum = $top_objctive ? $top_objctive->max_sum : 2000;
        $return = [
            ['comment', 'trim'],
            ['comment', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['comment', 'string', 'min' => 5, 'max' => 255, 'tooShort' => Yii::t('app','Не меньше пяти символов')],

            ['sum_end', 'trim'],
            ['sum_end', 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            ['sum_end', 'integer', 'min' => $min_sum, 'max' => $max_sum,  'message' => Yii::t('app','Сумма должна быть между') . ' ' . $min_sum . '$ ' . Yii::t('app','и') . ' ' . $max_sum . '$', 'tooBig' => Yii::t('app','Поставьте для начала небольшую цель, которую реально достигнуть исходя из баланса')],
            [['user_id','sum_start','image', 'date_end'], 'safe']

        ];
        if(!$this->image) {
            $return[] = ['image_file', 'required', 'message'=>Yii::t('app','Загрузите изображение')];
        }
        return $return;
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

    public function getData() {
        $objective = Objective::find()->where('max_sum >= '.$this->sum_end)->orderBy('max_sum DESC')->one();

        $this->data = ObjectiveStage::find()
            ->where(['objective_id' => $objective->id])
            ->andWhere('stage <= '.$this->percent)
            ->orderBy('stage DESC')
            ->one();
    }

    public function saveObjective() {
        if(!$this->validate()) {
            var_dump($this->errors);
            die;
            return false;
        }
        $user = Yii::$app->user->identity;
        $this->user_id = $user->id;
        $this->sum_start = Servis::getInstance()->beautyDecimal($user->getBalance());

        if($this->image_file) {
            $newPath = static::realizePath();
            $filename = $this->image_file->baseName . '.' . $this->image_file->extension;
            $path = dirname(dirname(__DIR__)) . '/frontend/web/upload/objectives/'. $newPath .$filename;
            $this->image_file->saveAs($path);


            $image_size = getimagesize($path);
            $image_side = $image_size[0] > $image_size[1] ? $image_size[1] + 1 : $image_size[0] + 1;
            Servis::cropImage($path, dirname(dirname(__DIR__)) . '/frontend/web/upload/objectives/'. $newPath . $this->image_file->baseName .'_cropped.'.$this->image_file->extension, $image_side, $image_side);
            unlink($path);
            $this->image =  '/upload/objectives/'. $newPath . $this->image_file->baseName .'_cropped.'.$this->image_file->extension;
        }
        if($this->save()) {
            return $this;
        }
        return false;

    }


    static function realizePath()
    {
        $pathToObjectives = dirname(dirname(__DIR__)) . '/frontend/web/upload/';
        if (!is_dir($pathToObjectives . 'objectives')) {
            mkdir($pathToObjectives . 'objectives', 0755);
        }
        $pathToUpload = dirname(dirname(__DIR__)) . '/frontend/web/upload/objectives/';
        $path1 = Servis::getInstance()->randomCode();
        $path2 = Servis::getInstance()->randomCode();
        if (!is_dir($pathToUpload . $path1)) {
            mkdir($pathToUpload . $path1, 0755);
        }
        if (!is_dir($pathToUpload . $path1 . '/' . $path2)) {
            mkdir($pathToUpload . $path1 . '/' . $path2, 0755);
        }
        return $path1 . '/' . $path2 . '/';
    }

}
