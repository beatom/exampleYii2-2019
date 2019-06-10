<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $notification
 * @property integer $type
 * @property string $status
 * @property date $date_add
 */
class Notification extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notification';
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

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    public function add( $user_id, $notification, $type=0){
        $this->user_id = $user_id;
        $this->notification = $notification;
        $this->type = $type;

        $this->save();

        $partner = User::findIdentity($user_id);
        if($partner && $partner->partner_id){
            $tmp = new Notification();
            $tmp->add($partner->partner_id, $notification, $type);
        }

        return true;
    }

    public static function getNewNotification($user_id, $set_viewed = true){
        $res = static::find()->where('user_id = '.$user_id.' AND status = 0')->orderBy('date_add DESC')->all();
        if($set_viewed){
            foreach ($res as $item){
                $item->status = 1;
                $item->save();
            }
        }
        return $res;
    }

    public static function getOldNotification($user_id, $limit=null){
        $res = static::find()->where('user_id = '.$user_id.' AND status = 1')->orderBy('date_add DESC');
        if($limit){
            $res->limit($limit);
        }
        return $res->all();
    }

    public static function getHtmlOldNotifakation($user_id){
        $items = self::getOldNotification($user_id);
        $html = '';
        foreach ($items as $item){
            $html .= '<li class="c-notifications__event">'.$item->notification.'</li>';
        }
        return $html;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }


}
