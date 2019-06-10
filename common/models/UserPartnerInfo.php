<?php
namespace common\models;

use common\models\trade\Investment;
use common\service\Servis;
use yii\db\ActiveRecord;
use common\models\Notification;
use Yii;

/**
 * User model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $sum_in_mount
 * @property integer $sum_in_all
 * @property integer $count_lower_partners
 * @property integer $personal_contribution
 * @property integer $contribution_p3
 * @property integer $personal_contribution_all
 * @property integer $attraction_investors_item
 * @property integer $attraction_partner_item
 * @property integer $attraction_investors_count
 * @property integer $attraction_partner_count
 * @property string $ids_partner
 * @property string $arr_line
 * @property string $all_partners
 * @property string $piramida
 *
 */
class UserPartnerInfo extends ActiveRecord
{

    public static function fix(){
        $out = new \stdClass();
        $out->sum_in_mount = 0;
        $out->sum_in_all = 0;
        $out->count_lower_partners = 0;
        $out->personal_contribution = 0;
        $out->contribution_p3 = 0;
        $out->personal_contribution_all = 0;
        $out->attraction_investors_item = 0;
        $out->attraction_partner_item = 0;
        $out->attraction_investors_count = 0;
        $out->attraction_partner_count = 0;
        $out->ids_partner = '';
        return $out;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_partner_info';
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
    public static function findIdentityUserId($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    public static function convert($user_info){
	    $out = new \stdClass();
	    $out->id = $user_info->id;
	    $out->user_id = $user_info->user_id;
	    $out->sum_in_mount = $user_info->sum_in_mount;
	    $out->sum_in_all = $user_info->sum_in_all;
	    $out->count_lower_partners = $user_info->getLowerPartners();
	    $out->personal_contribution = $user_info->personal_contribution;
	    $out->contribution_p3 = $user_info->contribution_p3;
	    $out->personal_contribution_all = $user_info->personal_contribution_all;
	    $out->attraction_investors_item = $user_info->attraction_investors_item;
	    $out->attraction_partner_item = $user_info->attraction_partner_item;
	    $out->attraction_investors_count = $user_info->attraction_investors_count;
	    $out->attraction_partner_count = $user_info->attraction_partner_count;
	    $out->ids_partner = $user_info->ids_partner;
	    $out->arr_line = unserialize($user_info->arr_line);
	    $out->all_partners = $user_info->all_partners;
	    $out->piramida = unserialize($user_info->piramida);

	    return $out;

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
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }



    /**
     * установит сумму привлеченных средств пользователем за месяц
     */
    public static function monthlyContributions( $punct = 0 ){

        $limit = 20;
        $ofset = 0;
        $flag = true;
        while ($flag){
            $sql = 'SELECT `id` FROM `user` LIMIT ' . $ofset . ', ' . $limit;
            $arr_ids = Yii::$app->db->createCommand($sql)->queryAll();
            $ofset = $ofset+$limit;
            if(empty($arr_ids)){
                $flag = false;
            }else{
                foreach ($arr_ids as $item){
                    $user_partner = UserPartnerInfo::findIdentityUserId($item['id']);
                    if(!$user_partner){
                        $user_partner = new UserPartnerInfo();
                        $user_partner->user_id = $item['id'];
                    }

                    if($punct == 3 ){
	                    $res = BalanceLog::getMonthlyContributionsP3( $user_partner->ids_partner );
	                    $user_partner->contribution_p3 = $res;
                    }
                    else if($punct == 2 )  {
	                    $res = BalanceLog::getMonthlyContributionsP2( $user_partner->ids_partner );
	                    $user_partner->sum_in_mount = $res;
	                    //hz
                        $res = BalanceLog::getMonthlyContributionsP2( $user_partner->ids_partner , true );
	                    $user_partner->sum_in_all = $res;
                    }
	                $user_partner->save();

                }
            }
        }
    }

    /**
     * установит сумму внесенных средств пользователем
     */
    public static function PersonalContribution(){

        $limit = 20;
        $ofset = 0;
        $flag = true;
        while ($flag){
            $sql = 'SELECT `id` FROM `user` GROUP BY `id` LIMIT '.$ofset.', '.$limit;
            $arr_ids = Yii::$app->db->createCommand($sql)->queryAll();
            $ofset = $ofset+$limit;
            if(empty($arr_ids)){
                $flag = false;
            }else{
                foreach ($arr_ids as $item){
                    $user_partner = UserPartnerInfo::findIdentityUserId($item['id']);
                    if(!$user_partner){
                        $user_partner = new UserPartnerInfo();
                        $user_partner->user_id = $item['id'];
                    }
                    $user_partner->personal_contribution = BalanceLog::getMonthlyContributionsP2( $item['id'], true);
                    $user_partner->personal_contribution_all = BalanceLog::getMonthlyContributions($item['id'], true,false);
                    $user_partner->save();
                }
            }
        }
    }
	/**
	 * Сумма которая сейчас в Ду м Готовом решении по таблице партнер инфо
	 */
	public static function getMonthlyContributionsFoTable( $user_ids){

		if(empty($user_ids)){
			$user_ids = 0;
		}
		$user_ids_tmp = $user_ids;


		$sql                      = 'user_id IN ( ' .  $user_ids  . ')';

		$res = UserPartnerInfo::find()
		               ->select( 'sum(personal_contribution) as summ' )
		               ->where( $sql )
		               ->createCommand()
		               ->queryScalar();

		$res = ($res < 0 )? 0 : $res;
		$res = ( $res )? $res: 0;

		return $res;
	}

    /**
     * считает количество партнеров на статус ниже
     */
    public static function countLowerPartners(){

        $limit = 20;
        $ofset = 0;
        $flag = true;
        while ($flag){
            $sql = 'SELECT `id`, `status_in_partner` FROM `user` GROUP BY `id` DESC LIMIT '.$ofset.', '.$limit;
            $arr_ids = Yii::$app->db->createCommand($sql)->queryAll();
            $ofset = $ofset+$limit;
            if(empty($arr_ids)){
                $flag = false;
            }else{
                foreach ($arr_ids as $item){
                    $user_partner = UserPartnerInfo::findIdentityUserId($item['id']);
                    if(!$user_partner){
                        $user_partner = new UserPartnerInfo();
                        $user_partner->user_id = $item['id'];
                    }
                    $user_partner->count_lower_partners = $user_partner->getLowerPartners();
                    $user_partner->save();
                }
            }
        }
    }


    public function getLowerPartners(){
        if(!$user = User::findIdentity($this->user_id)) {
            return 0;
        }
        $count = User::find()
            ->where(['partner_id' => $user->id])
            ->andWhere(['>=', 'status_in_partner', $user->status_in_partner])
            ->count();
        return $count ? $count : 0;
    }


    public static function getInfoBonusMounth($user_id){
        $info = UserPartnerInfo::findIdentityUserId($user_id);
        $data = Servis::getInstance()->getArrBonusesForMonth();
        $count = count($data);

        if(empty($info) ){
            return ['bonus'=>$data[$count-1]['bonus'], 'summ'=>$data[$count-1]['summ'], 'pay'=>0];
        }

        for ($i=0; $i<$count; $i++){
            if($i== 0 && $data[$i]['summ'] < $info->contribution_p3){
                return ['bonus'=>$data[$i]['bonus'], 'summ'=>0, 'pay'=>$data[$i]['bonus']];
            }

            if($info->contribution_p3 > $data[$i]['summ']){
                return ['bonus'=>$data[$i-1]['bonus'], 'summ'=> ($data[$i-1]['summ'] - $info->contribution_p3), 'pay'=>$data[$i]['bonus']];
            }
        }
        return ['bonus'=>$data[$count-1]['bonus'], 'summ'=>($data[$count-1]['summ'] - $info->contribution_p3), 'pay'=>0];
    }

    /**
     * @param $user_id
     * @param $partner_id
     */
    public static function addPartner($user_id, $partner_id){

    	if($user_id == $partner_id){
    		return false;
	    }

        $user_partner = UserPartnerInfo::findIdentityUserId($partner_id);
        if(!$user_partner){
            $user_partner = new UserPartnerInfo();
            $user_partner->user_id = $partner_id;
        }

        if($user_partner->ids_partner) {
            $arr = explode(',', $user_partner->ids_partner);
            $arr[] = $user_id;
        }
        else{
            $arr = [$user_id];
        }

        $user_partner->ids_partner = implode(',', $arr);
        $user_partner->save();

        $user = User::findIdentity($user_id);
        $user->partner_id = $partner_id;
        $user->save();

//        $notificaion = new Notification();
//        $mes = '<b>'.$user->username.'<span>зарегистрировался в вашей структуре</span></b>';
//        $notificaion->add($partner_id, $mes, 1);

    }

    public static function getTree($user_id){
        $tree = static::findIdentityUserId($user_id);
        $out = [];
        $out['structure'] = null;
        $out['lines_partner'] = null;
        $out['all_partner_ids'] = null;
        $out['ids_partner'] = null;
        if(!$tree) {
            return $out;
        }
        $out['structure'] = (!empty($tree->piramida))? unserialize( $tree->piramida) : [];
        $out['lines_partner'] = (!empty($tree->arr_line))? unserialize( $tree->arr_line) : [];
        $out['all_partner_ids'] = (!empty($tree->all_partners))? explode(',', $tree->all_partners) : [];
        $out['ids_partner'] = (!empty($tree->ids_partner))? explode(',', $tree->ids_partner) : [];
        return $out;
    }
    public static function getAllPartner($user_id){
        $tree = static::findIdentityUserId($user_id);
        if(!$tree)
            return [];
        return (!empty($tree->all_partners))? explode(',', $tree->all_partners) : [];
    }

    public static function tree($user_id){
        $out = [];
        $out['all_partner_ids'] = [];
        $out['lines_partner']=[];
      //  $out['structure']=[];

        $res = static::getChilds($user_id);
        if(!empty($res)) {
            $temp = [];
            $temp[] = $user_id;
            $ids_user = implode(',', $temp);

            $i=1;
            $lines = [];
            while (!empty($ids_user)) {

                $sql = 'SELECT id from `user` WHERE `partner_id` IN (' . $ids_user . ')';
                $ids_user = Yii::$app->db->createCommand($sql)->queryAll();

                if (!empty($ids_user)) {
                    $tmp = [];
                    foreach ($ids_user as $item) {
                        if ($item['id']) {
                            $tmp[] = $item['id'];
                            $out['all_partner_ids'][] = $item['id'];
                        }
                    }
                    $lines[$i] = $tmp;
                    $i++;
                    $ids_user = implode(',', $tmp);
                }
            }
            $out['lines_partner'] = $lines;


        }

        $structure = UserPartnerInfo::findIdentityUserId($user_id);
        if(!$structure){
            $structure = new UserPartnerInfo();
            $structure->user_id = $user_id;
        }

        $structure->all_partners = implode(',', $out['all_partner_ids']);
        $structure->arr_line = serialize($out['lines_partner']);
        $structure->save();
        return $structure;
    }
    public static function getChilds($partner_id){
        $sql = 'SELECT u.`id`, u.`partner_id`, u.`status_in_partner`, u.`username`, upi.sum_in_all
                FROM `user` u
                LEFT JOIN `user_partner_info` upi ON u.id = upi.user_id
                WHERE `partner_id` = '.$partner_id.' 
                ORDER BY u.`status_in_partner` DESC, upi.sum_in_all DESC';

        $arr_ids = Yii::$app->db->createCommand($sql)->queryAll();
        return $arr_ids;
    }

    public static function getSumInTradeAcaunt($user_id){
        $ids_accaunt = Yii::$app->db->createCommand('SELECT `id` from `trading_account` where `user_id` = '.$user_id )->queryAll();
        if(empty($ids_accaunt)){
            return 0;
        }
        $ids = [];
        foreach ($ids_accaunt as $item){
            $ids[]=$item['id'];
        }

        return Investment::getSumForTradAcaunt($ids);
    }

    /**
     *  построит структуру
     */
    public static function addIdsPartner(){

        $limit = 20;
        $ofset = 0;
        $flag = true;
        while ($flag){
            $sql = 'SELECT `id`, `status_in_partner` FROM `user`  LIMIT '.$ofset.', '.$limit;
            $arr_ids = Yii::$app->db->createCommand($sql)->queryAll();
            $ofset = $ofset+$limit;
            if(empty($arr_ids)){
                $flag = false;
            }else{
                foreach ($arr_ids as $item){

                    $partners = User::getPartners($item['id']);
                    $user_partner = UserPartnerInfo::findIdentityUserId($item['id']);

                    if(!$user_partner){
                        $user_partner = new UserPartnerInfo();
                        $user_partner->user_id = $item['id'];
                    }

                    $ids = [];
                    foreach ($partners as $partner){
                        $ids[] = $partner->id;
                    }
                    $user_partner->ids_partner = implode(',', $ids);
                    $user_partner->save();
                }
            }
        }
    }

    public static function BuildTreePartner(){

        $limit = 10;
        $ofset = 0;
        $flag = true;
        while ($flag){
            $sql = 'SELECT `id` FROM `user`  LIMIT '.$ofset.', '.$limit;
            $arr_ids = Yii::$app->db->createCommand($sql)->queryAll();
            $ofset = $ofset+$limit;
            if(empty($arr_ids)){
                $flag = false;
            }else{
                foreach ($arr_ids as $item){

                    $partners = UserPartnerInfo::tree($item['id']);

                }
            }
        }
    }


}
