<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $file
 * @property date $date_add
 * @property string $base_image
 *
 */
class UsersDocumentsUploaded extends ActiveRecord
{


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'users_documents_uploaded';
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

	public function getUser()
	{
		return $this->hasOne(User::class, ['id' => 'user_id']);
	}

	public static function add($user_id, $path, $base_image = null){
		$doc = new static();
		$doc->file = $path;
		$doc->user_id = $user_id;
		$doc->base_image = $base_image;

		return ($doc->save())? true : false;
	}

}
