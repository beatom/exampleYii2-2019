<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id_from
 * @property integer $user_id_to
 * @property integer $summ
 * @property string $description
 * @property date $date_add
 */
class PartnerBasicIncome extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'partner_basic_income';
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

	public static function add($summ, $user_id_to, $mess, $user_id_from)
	{

		$newLog = new static();
		$newLog->user_id_to = $user_id_to;
		$newLog->user_id_from = $user_id_from;
		$newLog->summ = $summ;
		$newLog->description = $mess;
		if($newLog->save()) {
			return $newLog;
		}
		return false;
	}

}
