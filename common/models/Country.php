<?php
namespace common\models;

use yii\db\ActiveRecord;
use common\models\City;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 *
 */
class Country extends ActiveRecord
{

    public static function tableName()
    {
        return 'country';
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

    public function getCities()
    {
        return $this->hasMany(City::className(), ['country_id' => 'id']);
    }

    public static function findCountry($string)
    {
        return static::find()->where("name LIKE '".$string."%'")->asArray()->all();
    }

    public static function getCountry($id)
    {
        $country = static::findIdentity($id);
        return $country ? $country->name : '';
    }

    public static function getAssotsArr(){
	    $tmp = Country::find()->all();
	    $out = array();
	    foreach ($tmp as $item){
		    $out[$item->id] = $item;
	    }
	    return $out;
    }

}
