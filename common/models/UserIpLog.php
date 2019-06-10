<?php
namespace common\models;

use common\service\api\AmoCrm;
use yii\db\ActiveRecord;
use common\models\User;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;
use Yii;
/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $ip
 * @property date $date_add
 * @property string $browser
 * @property string $browser_version
 * @property string $os
 * @property string $os_version
 * @property string $session_id
 */
class UserIpLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_ip_log';
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

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

//        if ($insert) {
//            $user = User::findIdentity($this->user_id);
//            if(!$user OR !$user->amo_contact_id){
//                AmoCrm::getInstance()->updateUser($user, ['ip']);
//            }
//        }
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
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

    public static function findLogs($user_id, $limit = 5 ){
        return static::find()->where(['user_id'=> $user_id])->orderBy('date_add DESC')->limit($limit)->all();
    }

   public static function setLog($user){
       $browser = new Browser();
       $os = new Os();

       $model = new static();
       $model->user_id = $user->id;
       $model->ip = $_SERVER['REMOTE_ADDR'];
       $model->date_add = date("Y-m-d H:i:s", time());
       $model->browser = $browser->getName();
       $model->browser_version = $browser->getVersion();
       $model->os = $os->getName();
       $model->os_version = $os->getVersion();
       $model->session_id = Yii::$app->session->getId();
       $model->save();
   }

   public static function getLastDate($user_id){
    	$tmp = UserIpLog::find()
		    ->where('user_id = '. $user_id)
		    ->orderBy('date_add')
		    ->limit(1)
		    ->all();
    	if(empty($tmp))
    		return null;
    	return $tmp[0]->date_add;
   }


}
