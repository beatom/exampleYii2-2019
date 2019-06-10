<?php
namespace common\models;

use backend\models\EditUserForm;
use yii\db\ActiveRecord;
use common\models\User;
use Yii;
use common\service\Servis;

/**
 * User model
 *
 * @property integer $id
 * @property string $name
 * @property string $phone
 * @property string $avatar
 * @property string $email
 * @property boolean $is_main
 * @property integer $amo_user_id
 * @property string $position
 */
class ManagerCard extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'manager_card';
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
            [['email', 'name', 'phone'], 'required', 'message'=>Yii::t('app','Необходимо заполнить')],
            [['email', 'name', 'phone', 'position'], 'trim'],
            [['name', 'phone', 'position'], 'string'],
            ['email', 'email'],
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

    public static function setManagers($old_manager_id = false)
    {
        $users = $old_manager_id ? User::find()->where(['manager_card_id' => $old_manager_id]) : User::find();
        foreach ($users->all() as $u) {
            $u->manager_card_id = static::getManagerId($u->id);
            $u->save();
        }
        return true;
    }

    public static function getUserManagerCard($user_id) {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }
        $card = null;
        if($user->manager_card_id) {
            $card = static::findIdentity($user->manager_card_id);
        }
        if(!$card) {
            return false;
            $card_id = static::getManagerId($user_id);
            $card = static::findIdentity($card_id);
            $user->manager_card_id = $card->id;
            $user->save();
        }
        return $card;
    }

    public static function getUserManager($user_id)
    {
        if(!$user = User::findIdentity($user_id)) {
            return false;
        }
        if(!$user->manager_card_id) {
            $user->manager_card_id = static::getManagerId($user_id);
            $user->save();
        }
        return $user->manager_card_id;
    }

    public static function getManagerId($user_id) {
        if($main_manager = static::find()->where(['is_main' => 1])->one()) {
            return $main_manager->id;
        } else {
            if(!$user = User::findIdentity($user_id) ) {
                return false;
            }
            $managers = static::find()->select('id')->orderBy('id')->all();

            if(!$managers) {
                return false;
            }
            $managers_ids = [];
            foreach ($managers as $m) {
                $managers_ids[] = $m->id;
            }
            $cursor = 0;
            if(!$prev_user = User::find()->where(['<', 'id', $user_id])->andWhere('manager_card_id IS NOT NULL')->orderBy('id DESC')->one()) {
                return $managers_ids[0];
            }

            if($key = array_search($prev_user->manager_card_id, $managers_ids) OR $key === 0) {
                if(!empty($managers_ids[$key+1])) {
                    $cursor = $key + 1;
                }
            }

            return $managers_ids[$cursor];
        }
    }


    public static function getManagers() {
        $managers = static::find()->select('id, name')->orderBy('id')->all();

        if(!$managers) {
            return [];
        }
        $managers_names = [];
        foreach ($managers as $m) {
            $managers_names[$m->id] = $m->name . ' id:' . $m->id;
        }

        return $managers_names;
    }

    public function edit() {
       if(!$this->validate()) {
           return false;
       }


        $uploaddir = $_SERVER['DOCUMENT_ROOT'] . '/../../frontend/web/upload/manager-cards/';

        if(!file_exists($uploaddir)){
            mkdir($uploaddir, 0755);
        }

        $name = $_FILES['ManagerCard']['name']['avatar'];
        $new_name = Servis::getInstance()->randomCode(12) . '.jpg';
        $uploadfile = $uploaddir . $new_name;


        if (move_uploaded_file($_FILES['ManagerCard']['tmp_name']['avatar'], $uploadfile)) {

            $this->avatar =  '/upload/manager-cards/'.$new_name;

        }

        return $this->save();
    }


}
