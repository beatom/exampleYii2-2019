<?php
namespace common\models;

use yii\db\ActiveRecord;


/**
 * User model
 *
 * @property integer $id
 * @property integer $objective_id
 * @property integer $stage
 * @property string $title
 * @property string $title_en
 * @property string $description
 * @property string $description_en
 */
class ObjectiveStage extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'objective_stage';
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
            [['stage','title','description'],'required'],
            ['stage','integer', 'min' => 0, 'max' => 100, 'message' => 'Процент должен быть от 0 до 100'],
      //      ['stage', 'unique', 'message' => 'Уровень с таким процентом уже существует'],
            [['title_en','description_en'], 'safe']
        ];
    }

    public function attributeLabels()
    {
        return [
            'objective_id' => 'Выберите цель',
            'stage' => 'Процент достижения',
            'title' => 'Заголовок ',
            'title_en' => 'Заголовок en',
            'description' => 'Текст',
            'description_en' => 'Текст en',
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
