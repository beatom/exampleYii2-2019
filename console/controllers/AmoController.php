<?php

namespace console\controllers;

use common\models\AmoQueue;
use common\models\ManagerCard;
use common\models\trade\InvestmentLog;
use common\models\User;
use common\service\api\AmoCrm;
use yii\console\Controller;
use Yii;
use common\service\LogMy;
use common\models\BalanceLog;
use common\models\AmoUserPipelines;
use common\models\trade\TradingAccount;

class AmoController extends Controller
{

    public $managers = [];

    public function actionTest()
    {
        $amoCrm = AmoCrm::getInstance();
        $lead = $amoCrm->getPipelines();
        var_dump($lead['response']['pipelines'][1601338]['statuses']);
    }


    public function actionSetManagers()
    {
        $amoCrm = AmoCrm::getInstance();
        $response = $amoCrm->getUsers();
        if (!isset($response['_embedded']['users'])) {
            return;
        }
        foreach ($response['_embedded']['users'] as $m) {
            if (!$manager = ManagerCard::find()->where(['name' => $m['name']])->orWhere(['email' => $m['login']])->one()) {
                continue;
            }
            $manager->amo_user_id = $m['id'];
            $manager->save();
        }
    }


    public function actionSetUsers()
    {
        $amoCrm = AmoCrm::getInstance();
        $offset = 0;
        $limit = 500;
        $work = true;
        do {
            $response = $amoCrm->getContactsList($offset);
            if (!isset($response['_embedded']['items'])) {
                return;
            }
            $users = $response['_embedded']['items'];
            $work = (count($users) >= $limit) ? true : false;
            foreach ($users as $u) {
                if ($user = $amoCrm->findUser($u)) {
                    if (!empty($u['leads'])) {
                        $lead_id = $u['leads']['id'][0];
                        $lead = $amoCrm->getLeadId($lead_id);
                        if (isset($lead['_embedded']['items'][0]['pipeline']['id'])) {
                            $pipeline_id = $lead['_embedded']['items'][0]['pipeline']['id'];
                            if (in_array($pipeline_id, $amoCrm->pipelines_stage)) {
                                foreach ($amoCrm->pipelines_stage as $key => $value) {
                                    if ($value == $pipeline_id) {
                                        $user->amo_contact_stage = $key;
                                    }
                                }
                                if (!$user->amo_contact_stage) {
                                    $user->amo_contact_stage = 3;
                                }
                            } else {
                                $user->amo_contact_stage = 3;
                            }
                        }
                    }
                    $user->amo_contact_id = $u['id'];
                    $user->save();
                }
            }
            $offset += $limit;
        } while ($work);
    }

    public function getManagers()
    {
        foreach (ManagerCard::find()->where('amo_user_id IS NOT NULL')->all() as $m) {
            $this->managers[$m->amo_user_id] = $m->id;
        }
    }


    public function actionGetUserMainLeads($user_start_id = false)
    {
        $start = microtime(true);
        $amoCrm = AmoCrm::getInstance();
        $leads = ['update' => []];
        $contacts = [
            'update' => []
        ];


        $u_array = User::find()
            ->where('amo_contact_id IS NOT NULL');
        if ($user_start_id) {
            $u_array->andWhere('id >= ' . $user_start_id);
        }
        foreach ($u_array->all() as $u) {
            $data = $amoCrm->getUpdateContactsResponsible($u->id);
            if (!$data) {
                continue;
            }
            foreach ($data['leads'] as $l) {
                $leads['update'][] = $l;
            }
            if ($data['contacts']) {
                $contacts['update'][] = $data['contacts'];
            }

            if (count($contacts['update']) > 5) {
                $amoCrm->setContacts($contacts);
                LogMy::getInstance()->setLog(['message' => "contacts update"], 'amo_responsible');
                $contacts['update'] = [];
            }

            if (count($leads['update']) > 5) {
                $amoCrm->updateLead($leads);
                LogMy::getInstance()->setLog(['message' => "leads update"], 'amo_responsible');
                $leads['update'] = [];
            }
        }
        if (!empty($leads['update'])) {
            LogMy::getInstance()->setLog(['message' => "leads update"], 'amo_responsible');
            $amoCrm->updateLead($leads);
        }
        if (!empty($contacts['update'])) {
            LogMy::getInstance()->setLog(['message' => "contacts update"], 'amo_responsible');
            $amoCrm->setContacts($contacts);
        }
        echo 'Время обновления управляющих: ' . round(microtime(true) - $start, 4) . ' сек.' . "\n";
    }

    public function actionUpdateUserManagers()
    {
        $start = microtime(true);
        $this->getManagers();
        $amoCrm = AmoCrm::getInstance();
        $offset = 0;
        $limit = 500;
        $work = true;
        do {
            $response = $amoCrm->getContactsList($offset);
            if (!isset($response['_embedded']['items'])) {
                return;
            }
            $users = $response['_embedded']['items'];
            $work = (count($users) >= $limit) ? true : false;
            foreach ($users as $u) {
                if ($user = $amoCrm->findUser($u)) {
                    if (isset($this->managers[$u['responsible_user_id']])) {
                        $user->manager_card_id = $this->managers[$u['responsible_user_id']];
                    }
                    $user->amo_contact_id = $u['id'];
                    $user->save();
                }
            }
            $offset += $limit;
        } while ($work);
        echo 'Время обновления управляющих: ' . round(microtime(true) - $start, 4) . ' сек.' . "\n";
    }


    public function actionSetCustomFields()
    {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->saveFields();
    }

    public function actionAddUserLead($user_id, $data)
    {
        $amoCrm = AmoCrm::getInstance();
        if (!$user = User::findIdentity($user_id) OR !$user->amo_contact_id) {
            return false;
        }
        $amo_contact_id = $user->amo_contact_id;
        $responsible_user_id = isset($data['responsible_user_id']) ? $data['responsible_user_id'] : ManagerCard::findIdentity(ManagerCard::getUserManager($user->id))->amo_user_id;
        $sum = isset($data['sum']) ? $data['sum'] : 0;
        $pipeline_id = isset($data['pipeline_id']) ? $data['pipeline_id'] : 1340179;
        $status_id = isset($data['status_id']) ? $data['status_id'] : 21577060;
        $amoCrm->addLead($amo_contact_id, $responsible_user_id, $sum, $pipeline_id, $status_id);
    }

    public function actionChangeUserLead($user_id, $data)
    {
        AmoCrm::changeUserLead($user_id, $data);
    }

    public function actionWorkQueue()
    {
        $this->actionUpdateUserEarned();
        do {
            $res = AmoQueue::findByNotWorket();
            $worked_ids = [];
            $update_users = [];
            $update_users_ids = [];

            $update_leads = [];
            $update_leads_ids = [];

            $another_work = [];
            foreach ($res as $item) {
                if ($item->task == 'actionUpdateUsers') {
                    $update_users_ids[] = $item->id;
                    $update_users[] = $item->additional_params;
                } elseif($item->task == 'actionUpdateLead') {
                    $update_leads[] = $item;
                    $update_leads_ids[] = $item->id;
                } else {
                    $another_work[] = $item;
                    $worked_ids[] = $item->id;
                }
            }
            if (empty($worked_ids) AND empty($update_users_ids) AND empty($update_leads_ids)) {
                echo 'нет задач' . PHP_EOL;
                return;
            }
            //AmoQueue::setWorked($worked_ids);

            if (!empty($update_users)) {
                $query = User::find()
                    ->select([
                        'user.id',
                        'user.username', //логин
                        'user.phone', //Мобильный
                        'user.email', //Email
                        'user.firstname', //ФИО
                        'user.lastname', //ФИО
                        'user.middlename', //ФИО
                        'user.date_bithday', //Дата рождения
                        'user.date_reg', //Дата регистрации
                        'user.amo_contact_id', // Amo crm contact id
                        'user.status_in_partner', // Должность
                        'user.balance_partner',
                        'user.amo_contact_stage',
                        'SUM(IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + IF(invested_today IS NOT NULL, invested_today, 0) + user.balance + user.balance_partner) AS balance', //Баланс
                        'user.country_id', //  Страна/город
                        'user.city_name', // Страна/город
                        'user.manager_card_id',
                        'user.amo_tag_level',
                        'user.earned',
                        'user.amo_name_level',
                        'q.params',  //данные которые обновляются
                        'IF(first_deposit_sum IS NOT NULL ,first_deposit_sum, 0) as first_deposit', //сумма первого пополнения
                        'ip.ip', // последняя дата входа на сайт
                        'aup.synergy_1', //сделка в воронке "synergy"
                        'aup.meet_up_moscow', //сделка в воронке "Встречи (Москва)"
                        'aup.save_capital', //сделка в воронке "Save Capital"
                        'aup.meaningful_customer_card', //сделка в воронке Карта "Значимого Клиента"
                        'aup.plus_50', //сделка в воронке "+ 50% к депозиту"
                        'aup.loyalty_program', //сделка в воронке "Программа лояльности"
                        'aup.trading_school',
                        'aup.mailing_material',
                        'aup.vebinar_seminar',
                        'aup.save_capital_vebinar',
                    ])
                    ->leftJoin('(SELECT SUM(summ) as invested_today, ti.user_id FROM `traiding_investments_log` as ti
                LEFT JOIN investments as i on i.id = ti.investment_id
                WHERE i.date_add < CURDATE() AND type = 1 AND status = 2 AND datetime_add >= CURDATE()  AND ti.solution_id is NULL
                GROUP BY ti.user_id) as inv_td ON inv_td.user_id = user.id')
                    ->leftJoin('(SELECT SUM(summ_current) AS investments_summ, investments.user_id
                 FROM investments
                 LEFT JOIN `trading_account` as t ON investments.trading_account_id = t.id 
                 WHERE investments.bonus_money IS NULL 
                 AND investments.deleted = false
                 AND (t.type_account <> 4 OR t.id IS NULL)  AND investments.solution_id IS NULL
                 GROUP BY investments.user_id) AS i
                 ON user.id = i.user_id ')
                    ->leftJoin('(SELECT SUM(summ_current) AS solutions_summ, solutions.user_id
                 FROM investments as solutions
                 WHERE solutions.bonus_money IS NULL 
                 AND solutions.deleted = false
                 AND solutions.solution_id IS NOT NULL
                 GROUP BY solutions.user_id) AS i2
                 ON user.id = i2.user_id ')
                    ->leftJoin('amo_queue as q', 'q.additional_params = user.id')
                    ->leftJoin('(SELECT max(date_add) AS ip, user_ip_log.user_id
                 FROM user_ip_log
                 GROUP BY user_ip_log.user_id) AS ip
                 ON user.id = ip.user_id')
                    ->leftJoin('(SELECT balance_log.summ as first_deposit_sum, balance_log.user_id 
                    FROM balance_log
                    WHERE operation IN (0,3) 
                    AND balance_log.status = 1
                     AND system <> 5
                    GROUP BY balance_log.user_id 
                    ORDER BY balance_log.date_add ASC) as bl1 
                    ON bl1.user_id = user.id')
                    ->leftJoin('amo_user_pipelines as aup', 'aup.user_id = user.id')
                    ->where(['user.status' => 10, 'user.id' => $update_users, 'q.worked' => 0])
                    ->groupBy('user.id')
                    ->asArray();
                $data = $query->all();
                AmoQueue::setWorked($update_users_ids);
                $contact['update'] = [];
                foreach ($data as $d) {
                    $update = AmoCrm::getInstance()->prepareUpdateUserData($d);
                    $contact['update'][] = $update;
                }
                if (!empty($contact['update'])) {
                    AmoCrm::getInstance()->setContacts($contact);
                }
            }

            if (!empty($update_leads)) {
                AmoQueue::setWorked($update_leads_ids);
                $new_leads['update'] = [];
                foreach ($update_leads as $task) {
                    $new_leads['update'][] = unserialize($task->params);
                }

                if (!empty($new_leads['update'])) {
                    $answer = AmoCrm::getInstance()->updateLead($new_leads);
                    var_dump($answer);
                }
            }

            foreach ($another_work as $item) {
                $task = $item->task;
                $this->$task($item->params, unserialize($item->additional_params));
                AmoQueue::setWorked([$item->id]);
            }
        } while ($res);
    }

    public function actionUpdateUsersData()
    {
        $amoCrm = AmoCrm::getInstance();
        $data = ['phone', 'email', 'date_bithday', 'date_reg', 'firstname', 'lastname', 'middlename', 'country_id', 'city_name', 'status_in_partner', 'balance', 'ip', 'first_deposit'];
        foreach (User::find()->where('amo_contact_id IS NOT NULL')->all() as $u) {
            $amoCrm->updateUser($u, $data);
        }
    }

    public function actionCreateUsers($from)
    {
        $amoCrm = AmoCrm::getInstance();
        $data1 = ['phone', 'email', 'date_bithday', 'date_reg', 'firstname', 'lastname', 'middlename', 'country_id', 'city_name', 'status_in_partner', 'balance', 'ip', 'first_deposit'];
        $data2['ip'] = time();
        $data2['balance'] = 0;
        $data2['first_deposit'] = 0;
        foreach (User::find()->where('amo_contact_id IS NULL AND id >' . $from)->all() as $u) {
            $amoCrm->addUser($u, $data2);
            $u->refresh();
            $amoCrm->updateUser($u, $data2);
        }
        $this->actionUpdateUserManagers();
    }


    public function actionBigFix()
    {
        $user_ids = AmoQueue::find()->select('params')->where("`date_add` BETWEEN '2018-12-10 02:00:00' AND '2018-12-10 04:59:00' AND `task` = 'actionChangeUserLead'")->asArray()->all();
        $ids = [];
        foreach ($user_ids as $u_id) {
            $ids[] = $u_id['params'];
        }
        foreach (User::find()->where(['id' => $ids])->all() as $user) {
            $before = AmoQueue::find()->where("`date_add` < '2018-12-10 02:00:00' AND `task` = 'actionChangeUserLead' AND params = " . $user->id)->orderBy('date_add DESC')->one();
            if (!$before) {
                $user->amo_tag_level = 0;
                $user->save();
                continue;
            }
            $before_data = unserialize($before->additional_params);
            $user->amo_tag_level = isset($before_data['tag_level']) ? $before_data['tag_level'] : 0;
            $user->amo_contact_stage = $before_data['stage'];
            $user->save();
        }

        $amoCrm = AmoCrm::getInstance();
        $data = ['phone', 'email', 'date_bithday', 'date_reg', 'firstname', 'lastname', 'middlename', 'country_id', 'city_name', 'status_in_partner', 'balance', 'ip', 'first_deposit'];
        foreach (User::find()->where('amo_contact_id IS NOT NULL')->andWhere(['id' => $ids])->all() as $u) {
            $amoCrm->updateUser($u, $data);
        }
    }


    public function actionFixTags()
    {
        $amoCrm = AmoCrm::getInstance();
       // $data = ['phone', 'email', 'date_bithday', 'date_reg', 'firstname', 'lastname', 'middlename', 'country_id', 'city_name', 'status_in_partner', 'balance', 'ip', 'first_deposit'];
        $users = User::find()
            ->select(['user.*',
                'IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + IF(invested_today IS NOT NULL, invested_today, 0) as investments_summ',
                'IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + IF(invested_today IS NOT NULL, invested_today, 0) + user.balance AS total_b',
                //   'SUM(IF(investments_summ IS NOT NULL , investments_summ, 0) + user.balance + user.balance_partner) AS total_b_with_p'
            ])
            ->leftJoin('(SELECT SUM(summ_current) AS investments_summ, investments.user_id
                 FROM investments
                 LEFT JOIN `trading_account` as t ON investments.trading_account_id = t.id
                 WHERE investments.bonus_money IS NULL
                 AND investments.deleted = false
                 AND (t.type_account <> 4 OR t.id IS NULL) AND investments.solution_id IS NULL
                 GROUP BY investments.user_id) AS i
                 ON user.id = i.user_id ')
            ->leftJoin('investments as inv', 'inv.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as invested_today, ti.user_id FROM `traiding_investments_log` as ti
                LEFT JOIN investments as i on i.id = ti.investment_id
                WHERE i.date_add < CURDATE() AND type = 1 AND status = 2 AND datetime_add >= CURDATE() AND  i.solution_id IS NULL
                GROUP BY ti.user_id) as inv_td ON inv_td.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ_current) AS solutions_summ, solutions.user_id
                 FROM investments as solutions
                 WHERE solutions.bonus_money IS NULL 
                 AND solutions.deleted = false
                 AND solutions.solution_id IS NOT NULL
                 GROUP BY solutions.user_id) AS i2
                 ON user.id = i2.user_id ')
            ->where('user.id > 300 AND amo_tag_level > 0 AND amo_contact_id IS NOT NULL')
            ->andWhere('inv.deleted = false')
            ->groupBy('user.id')
            ->all();
        $i = 0;
        foreach ($users as $user) {
            $invested = $user->total_b - BalanceLog::find()->where(['user_id' => $user->id, 'operation' => [0, 1, 2, 3], 'status' => 1])->andWhere('summ < 0')->sum('summ');

            if ($invested < $amoCrm::tag_levels[$user->amo_tag_level]['bottom']) {
                foreach ($amoCrm::tag_levels as $key => $level) {
                    if ($invested < $level['top']) {
                        echo $user->id. ' ' . $user->amo_tag_level . ' -> ' . $key . "\n";
                        $user->amo_tag_level = $key;
                        $user->save();
                        $i++;
                        break;
                    }
                }
            }
        }
        var_dump('end '.$i);
    }

    public function actionGetEvents()
    {
        $amoCrm = AmoCrm::getInstance();
        $i = 0;
        $end_date = strtotime('2018-12-17 10:00:00');

        $offset = 0;
        $limit = 400;
        $work = true;
        $deals = [];
        $deals_ids = [];
        do {
            $response = $amoCrm->getEventsList($offset, $limit);
            if (!isset($response['_embedded']['items'])) {
                break;
            }
            $work = $response['_embedded']['items'][0]['created_at'] < $end_date ? true : false;
            foreach ($response['_embedded']['items'] as $el) {

                if ($end_date > $el['created_at'] AND $el['created_by'] == '2451640' AND $el['note_type'] == 3 AND in_array($el['params']['PIPELINE_ID_NEW'], ['1175350', '1174639']) AND !in_array($el['element_id'], $deals_ids)) {
                  //  var_dump($el);
                    $deals_ids[] = $el['element_id'];
                    $deals[$el['element_id']] =
                        [
                            'id' => $el['element_id'],
                            'pipeline' => $el['params']['PIPELINE_ID_OLD'],
                            'status' => $el['params']['STATUS_OLD']
                        ];
                    $i++;
                }
            }
            $offset += $limit;
        } while ($work);
        sort($deals_ids);
        var_dump(count($deals_ids));
        
        foreach ($deals_ids as $dl_id) {
            $d = $deals[$dl_id];
            $ld =  $amoCrm->getLeadId($d['id']);
            if(!$ld OR !isset($ld['_embedded']['items'][0]['name'])) {
                continue;
            }
            $name = $ld['_embedded']['items'][0]['name'];
            $update_array = array(
                'id' =>  $d['id'],
                'name' => $name,
                'updated_at' => time(),
                'status_id' => intval($d['status']),
                'pipeline_id' => intval($d['pipeline']),
            );

            $leads['update'] = array(
                $update_array
            );
            $amoCrm->updateLead($leads);
            var_dump($d);
        }
        var_dump($limit);
        var_dump($offset);
        var_dump($i);
    }


    public function actionUpdateUserLeads($user_id, $params) {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->updateUserLeads($user_id, $params);
    }


    public function actionUpdateUserEarned()
    {
        $query = 'UPDATE `user`,
                    (SELECT `user`.`id` as id, 
                    round(IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + user.balance + IF(invested_today IS NOT NULL, invested_today, 0) - (IF(dtp2 IS NOT NULL ,dtp2, 0) + IF(dtp1 IS NOT NULL ,dtp1 , 0)),2) as earned 
                    FROM `user` 
                    LEFT JOIN (SELECT SUM(summ_current) AS investments_summ, SUM(summ_show) AS investments_show, investments.user_id FROM investments LEFT JOIN `trading_account` as t ON investments.trading_account_id = t.id WHERE investments.bonus_money IS NULL AND investments.deleted = false AND (t.type_account <> 4 OR t.id IS NULL) AND investments.solution_id IS NULL GROUP BY investments.user_id) AS i ON user.id = i.user_id 
                    LEFT JOIN (SELECT SUM(summ) as invested_today, ti.user_id FROM `traiding_investments_log` as ti LEFT JOIN investments as i on i.id = ti.investment_id WHERE i.date_add < CURDATE() AND type = 1 AND status = 2 AND datetime_add >= CURDATE() AND ti.solution_id is NULL GROUP BY ti.user_id) as inv_td ON inv_td.user_id = user.id 
                    LEFT JOIN (SELECT SUM(summ_current) AS solutions_summ, solutions.user_id FROM investments as solutions WHERE solutions.bonus_money IS NULL AND solutions.deleted = false AND solutions.solution_id IS NOT NULL GROUP BY solutions.user_id) AS i2 ON user.id = i2.user_id   
                    LEFT JOIN (SELECT SUM(balance_log.summ) as dtp1, balance_log.user_id FROM balance_log WHERE operation IN (0,1,2,3,6) AND balance_log.status = 1 GROUP BY balance_log.user_id) as bl5 ON bl5.user_id = user.id 
                    LEFT JOIN (SELECT SUM(balance_log.summ) as dtp2, balance_log.user_id FROM balance_log WHERE operation = 1 AND balance_log.status IN (0,3) GROUP BY balance_log.user_id) as bl6 ON bl6.user_id = user.id 
                    WHERE user.status = 10 GROUP BY `user`.`id`
                    ) AS `src`
                SET
                    user.`earned`= src.earned
                WHERE
                    user.`id` = src.id;';
        Yii::$app->db->createCommand($query)->query();
    }

    public function actionUpdateLead() {
        $tasks = AmoQueue::find()->where(['task' => 'actionUpdateLead', 'worked' => 0])->all();

    }

    public function actionAddLeadMeaningfulCustomerCard($user_id) {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->addLeadMeaningfulCustomerCard($user_id);
    }

    public function actionCreateMoscowLead($user_id, $params = false) {
        $amoCrm = AmoCrm::getInstance();
       if($params) {
           $amoCrm->addLeadMoscow($user_id, $params);
       } else {
           $amoCrm->addLeadMoscow($user_id);
       }

    }

    public function actionAddLeadLoyaltyProgram($user_id) {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->addLeadLoyaltyProgram($user_id);
    }

    public function actionAddLeadSaveCapitalVebinar($user_id) {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->addLeadSaveCapitalVebinar($user_id);
    }

    public function actionAddLeadSaveCapital($user_id, $params = false) {
        $amoCrm = AmoCrm::getInstance();
        if($params) {
            $amoCrm->addLeadSaveCapital($user_id, $params);
        } else {
            $amoCrm->addLeadSaveCapital($user_id);
        }
    }
    
    public function actionChangeLeadTradingSchool($user_id) {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->updateLeadTradingSchool($user_id);
    }

    public function actionAddLeadPlusFifty($user_id, $stage) {
        $amoCrm = AmoCrm::getInstance();
        $amoCrm->addLeadPlusFifty($user_id, $stage);
    }

    public function actionCrmUpdate($start_id = 0) {
        $amoCrm = AmoCrm::getInstance();
        $data = ['phone', 'email', 'date_bithday', 'date_reg', 'firstname', 'lastname', 'middlename', 'country_id', 'city_name', 'status_in_partner', 'balance', 'ip', 'first_deposit', 'earned'];
        $responsible_user_id = AmoCrm::$main_manager_id;
       // $this->actionFixTags();
        foreach (User::find()->where('amo_contact_id IS NOT NULL AND id > '. $start_id)->all() as $user) {

            $name = $amoCrm::name_levels[$user->amo_name_level];
            $user_pipelines = AmoUserPipelines::getOrCreate($user->id);
            $new_tags = [ $amoCrm::tag_levels[$user->amo_tag_level]['title'] ] ;
            

            $leads['add'] = [];
            $user_leads = $amoCrm::getLeadsByAmoContactId($user->amo_contact_id);
            
            if (!$user_pipelines->synergy_1) {
                if (!$lead = $amoCrm::getPipelineLeadId($user_leads, 1601320)) {
                    $resp = ManagerCard::findIdentity($user->manager_card_id);
                    $leads['add'][] = [
                        'name' => $name,
                        'created_at' => time(),
                        'pipeline_id' => 1601320, // Synergy
                        'status_id' => 24370213, //Первичный контакт
                        'responsible_user_id' => $resp->amo_user_id,
                        'contacts_id' => $user->amo_contact_id,
                        'tags' => implode(',', $new_tags),
                        'request_id' => 1
                    ];
                } else {
                    $user_pipelines->synergy_1 = $lead['id'];
                }
            }
            if (!$user_pipelines->mailing_material) {
                if (!$lead = $amoCrm::getPipelineLeadId($user_leads, 1618561)) {
                    $leads['add'][] = [
                        'name' => $name,
                        'created_at' => time(),
                        'pipeline_id' => 1618561, // Рассылочный материал
                        'status_id' => 24594199, //Первичный контакт
                        'responsible_user_id' => $responsible_user_id,
                        'contacts_id' => $user->amo_contact_id,
                        'tags' => implode(',', $new_tags),
                        'request_id' => 2
                    ];
                } else {
                    $user_pipelines->synergy_1 = $lead['id'];
                }
            }
            if (!$user_pipelines->trading_school) {
                if (!$lead = $amoCrm::getPipelineLeadId($user_leads, 1592986)) {
                    $leads['add'][] = [
                        'name' => $name,
                        'created_at' => time(),
                        'pipeline_id' => 1592986, // Школа трейдинга
                        'status_id' => 24234025, //Первичный контакт
                        'responsible_user_id' => $responsible_user_id,
                        'contacts_id' => $user->amo_contact_id,
                        'tags' => implode(',', $new_tags),
                        'request_id' => 3
                    ];
                } else {
                    $user_pipelines->synergy_1 = $lead['id'];
                }

            }
            if (!$user_pipelines->vebinar_seminar) {
                if (!$lead = $amoCrm::getPipelineLeadId($user_leads, 1618564)) {
                    $leads['add'][] = [
                        'name' => $name,
                        'created_at' => time(),
                        'pipeline_id' => 1618564, // vebinar/seminar
                        'status_id' => 24594211, //Первичный контакт
                        'responsible_user_id' => $responsible_user_id,
                        'contacts_id' => $user->amo_contact_id,
                        'tags' => implode(',', $new_tags),
                        'request_id' => 4
                    ];
                } else {
                    $user_pipelines->vebinar_seminar = $lead['id'];
                }

            }

            if (!empty($leads['add'])) {
                $action = 'api/v2/leads';
                $response = $amoCrm->curl($action, $leads);

                if(!is_array($response['_embedded']['items']) OR !$response['_embedded']['items']) {
                    $response = $amoCrm->curl($action, $leads);
                }
                foreach ($response['_embedded']['items'] as $item) {
                    if(!isset($item['request_id'])) {
                        continue;
                    }
                    switch ($item['request_id']) {
                        case 1:
                            $user_pipelines->synergy_1 = $item['id'];
                            break;
                        case 2:
                            $user_pipelines->mailing_material = $item['id'];
                            break;
                        case 3:
                            $user_pipelines->trading_school = $item['id'];
                            break;
                        case 4:
                            $user_pipelines->vebinar_seminar = $item['id'];
                            break;
                    }
                }
            }
            $user_pipelines->save();
            if($user->training_complete) {
                AmoQueue::addTask('actionChangeLeadTradingSchool', $user->id);
            }

            $user_plus_50 = false;
            if($user_accounts = TradingAccount::find()->where(['user_id' => $user->id, 'type_account' => 2])->all()) {
                foreach($user_accounts as $user_account) {
                    if(InvestmentLog::find()->where(['trading_account_id' => $user_account->id, 'type' => 1, 'status' => 1, 'type_invest' => 1])->sum('summ') >= 10) {
                        AmoQueue::addTask('actionAddLeadPlusFifty', $user->id, serialize(3));
                        $user_plus_50 = true;
                        break;
                    }
                }
            }

            if(!$user_plus_50 AND $user_accounts = TradingAccount::find()->where(['user_id' => $user->id, 'type_account' => 2])->all()) {
                foreach($user_accounts as $user_account) {
                    if(InvestmentLog::find()->where(['trading_account_id' => $user_account->id, 'type' => 1, 'status' => 1, 'type_invest' => 1])->sum('summ') >= 5) {
                        AmoQueue::addTask('actionAddLeadPlusFifty', $user->id, serialize(2));
                        $user_plus_50 = true;
                        break;
                    }
                }

            }
            if(!$user_plus_50 AND $user_accounts = TradingAccount::find()->where(['user_id' => $user->id, 'type_account' => 4])->exists()) {
                AmoQueue::addTask('actionAddLeadPlusFifty', $user->id, serialize(1));

            }
            
            $amoCrm->updateUser($user, $data);
            echo "user updated ". $user->id . "\n";
        }
       
    }
}