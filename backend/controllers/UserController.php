<?php
namespace backend\controllers;

use common\models\BalanceBonusLog;
use common\models\BonusDebt;
use common\models\ChatMessage;
use common\models\ChatTemplate;
use common\models\Country;
use common\models\Overdraft;
use common\models\PartnerBaluLog;
use common\models\Sender;
use common\models\trade\Investment;
use common\models\trade\TradingAccount;
use common\models\User;
use common\models\UserDoc;
use common\models\UserIpLog;
use common\models\UserMessage;
use common\models\UserObjectives;
use common\models\UserPartnerInfo;
use common\service\Servis;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;
use common\models\BalanceLog;
use common\models\BalancePartnerLog;
use common\models\UsersDocumentsUploaded;
use common\models\UserSocial;
use backend\models\EditUserForm;
use common\models\PaymentCardRequest;
use common\models\ManagerCard;
use common\models\trade\ManagerReviews;
use common\models\trade\TradingPeriodLog;
use common\service\LogMy;
use common\models\trade\SolutionReviews;
use yii\web\UploadedFile;

/**
 * Site controller
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actionIndex()
    {
        $countrys = Country::getAssotsArr();
        $search_query = array();//operation

        $search_query['status'] = [User::STATUS_ACTIVE, User::STATUS_ADMIN];

        if (!empty($_GET['status']) && $_GET['status'] >= 0) {
            $search_query['status'] = $_GET['status'];
        }
        if (!empty($_GET['status_in_partner']) && $_GET['status_in_partner'] >= 0) {
            $search_query['status_in_partner'] = $_GET['status_in_partner'];
        }


        $query = User::find()
            ->select([
                'user.id',
                'user.username',
                "CONCAT_WS(' ', user.firstname, user.lastname, user.middlename ) as fio",
                'user.email',
                'user.phone',
                'CONCAT_WS("/", c.name, user.city_name) as adress',
                'user.date_reg',
                'user.date_bithday',
                'if(user.status = 1, "Администратор", "Пользователь") as role',
                'ROUND(user.balance,2) as balance',
                'ROUND(user.balance_bonus,2) as balance_bonus',
                'ROUND(user.balance_partner,2) as balance_partner',
                'ROUND(user.ball_invest,2) as ball_invest',
                'user.status_in_partner as stp',
                'ROUND(p.personal_contribution,2) as personal_contribution',
                'last_login',
            ])
            ->leftJoin('country as c', 'user.country_id = c.id')
            ->leftJoin('user_partner_info as p', 'user.id = p.user_id')
            ->leftJoin('(SELECT MAX(date_add) as last_login, user_id FROM `user_ip_log` GROUP BY user_id) as i ON i.user_id = user.id')
            ->asArray()
//	                 ->select('*')
            ->where($search_query);

        $query->leftJoin('user_partner_info', 'user_partner_info.user_id = user.id');

        if (!empty($_GET['username'])) {
            $query->andFilterWhere(['like', 'username', $_GET['username']]);
        }
        if (!empty($_GET['email'])) {
            $query->andFilterWhere(['like', 'email', $_GET['email']]);
        }
        if (!empty($_GET['user_id'])) {
            $query->andFilterWhere(['=', 'user.id', $_GET['user_id']]);
        }
        if (!empty($_GET['city'])) {
            $query->andFilterWhere(['like', 'city_name', $_GET['city']]);
        }
        if (!empty($_GET['date_from'])) {
            $query->andFilterWhere(['>=', 'date_reg', $_GET['date_from'] . ' 00:00:00']);
        }
        if (!empty($_GET['date_to'])) {
            $query->andFilterWhere(['<=', 'date_reg', $_GET['date_to'] . ' 00:00:00']);
        }

        if (!empty($_GET['fild']) && isset($_GET['order_by'])) {
            $sort = ($_GET['order_by']) ? 'DESC' : 'ASC';
            $query->orderBy($_GET['fild'] . ' ' . $sort);
        }

        if (isset($_GET['export'])) {
            $data = $query

                ->all();
//                ->createCommand()->rawSql;
//            var_dump($res);
//            die;
            $title = ['id', 'Логин', 'ФИО', 'email', 'Телефон', 'Страна/Город', 'Дата регистрации', 'Дата рожденья', 'Роль', 'Баланс', 'Бонус', 'Партнерский счет', 'Балы', 'Статус', 'Введено', 'Вход на сайт'];

//            $data = [];
//            foreach ($res as $item) {
//                $t = [];
//                $t[] = $item->id;
//                $t[] = $item->username;
//                $t[] = $item->firstname . ' ' . $item->lastname . ' ' . $item->middlename;
//                $t[] = $item->email;
//                $t[] = $item->phone;
//                $t[] = (($item->country_id !== null) ? $countrys[$item->country_id]->name . '/' : '') . $item->city_name;
//                $t[] = $item->date_reg;
//                $t[] = $item->date_bithday;
//                $t[] = ($item->status == 1) ? 'Администратор' : 'Пользователь';
//                $t[] = $item->balance;
//                $t[] = $item->balance_bonus;
//                $t[] = $item->balance_partner;
//                $t[] = $item->ball_invest;
//                $t[] = User::$partner_staus[$item->status_in_partner];
//
//                $partner_info = \common\models\UserPartnerInfo::findIdentityUserId($item->id);
//                $t[] = ($partner_info) ? $partner_info->personal_contribution : 0;
//                $t[] = UserIpLog::getLastDate($item->id);
//
//                $data[] = $t;
//            }
            foreach ($data as $key => $value) {
                $data[$key]['stp'] = User::$partner_staus[$value['stp']];
            }

            Servis::getInstance()->export($data, $title, 'users.csv');
        }

        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'users' => $models,
            'pages' => $pages,
            'countrys' => $countrys,
        ]);
    }

    public function actionUser($id)
    {
        $user = User::findIdentity($id);
        if (!$user) {
            return $this->redirect('/user/index');
        }

        if (Yii::$app->request->isPost AND $post = Yii::$app->request->post()) {
            if (isset($post['action'])) {
                if ($post['action'] == 'changepass') {
                    $user->setPassword(trim($post['pass']));
                    if ($user->save()) {
                        Yii::$app->session->setFlash('success', 'Пароль был изменен');
                    }
                }
                if ($post['action'] == 'changeutipemail' AND isset($post['utip_email']) AND $post['utip_email'] != '') {
                    $user->utip_email = trim($post['utip_email']);
                    if ($user->save()) {
                        Yii::$app->session->setFlash('success', 'Utip Email успешно изменен');
                    }
                }
                if ($post['action'] == 'change_amo_contact_id' AND isset($post['ammo_contact_id']) AND $post['ammo_contact_id'] != '') {
                    $user->utip_email = trim($post['ammo_contact_id']);
                    if ($user->save()) {
                        Yii::$app->session->setFlash('success', 'AmoCrm id успешно изменен');
                    }
                }
            }
        }

        return $this->render('user', [
            'user' => $user,
        ]);
    }

    public function actionEdit($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        $user->getManagerCard();
        $managers = ManagerCard::getManagers();
        $user->refresh();

        $social = UserSocial::findIdentityUserId($user->id);
        $model = new EditUserForm();
        $model->getSelectValue($user, $social);

        if ($model->load(Yii::$app->request->post())) {
            $model->avatar = UploadedFile::getInstance($model, 'avatar');
            if (isset($model->date_birthday)) {
                $model->date_birthday = date('Y-m-d H:i:s', strtotime($model->date_birthday));
            }
            $errors = false;
            if(User::find()->where(['username' => $model->username])->andWhere('id <> '.$id)->exists()) {
                $model->addError('username', 'Пользователь с таким ником уже существует');
                $errors = true;
            }
            if(User::find()->where(['email' => $model->email])->andWhere('id <> '.$id)->exists()) {
                $model->addError('email', 'Пользователь с таким email уже существует');
                $errors = true;
            }
            if (!$errors AND $model->validate()) {
                $updateuser = $model->saveChange($user, $social);
                $user = $updateuser['user'];
                $social = $updateuser['social'];
                Yii::$app->getSession()->setFlash('success', 'Пользователь успешно обновлен');
            }
        }

        $countries = Country::find()->all();
        $location['country'] = Country::find()->where(['id' => $model->country_id])->asArray()->one();

        return $this->render('edit', [
            'countries' => $countries,
            'location' => $location,
            'user' => $user,
            'model' => $model,
            'managers' => $managers,
            'seo'=>[
                'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }


    public function actionBalanceBonus($id)
    {
        $user = User::findIdentity($id);
        if (!$user) {
            return $this->redirect('/user/index');
        }


        if (Yii::$app->request->isPost AND $post = Yii::$app->request->post()) {

            if (isset($post['set-balanse-bonus']) && !empty($post['balance-bonus'])) {

                $comment = ($post['comment-bonus']) ? $post['comment-bonus'] : '';
                $summ = trim($_POST['balance-bonus']);

                $balanse_log = new BalancePartnerLog();
                $balanse_log->add($summ, $id, $comment);
                $balanse_log->save();
                $this->refresh();

            } else if (isset($post['set-balanse-ball']) && !empty($post['balance-ball'])) {
                $comment = ($post['comment-ball']) ? $post['comment-ball'] : '';
                $summ = trim($_POST['balance-ball']);

                $balanse_log = new PartnerBaluLog();
                $balanse_log->add($summ, $id, $comment);
                $this->refresh();
            }
        }

        $bonus_debts = BonusDebt::getUserDebts($id);

        return $this->render('balance-bonus', [
            'user' => $user,
            'bonus_debts' => $bonus_debts,
        ]);
    }


    public function actionMessages($id)
    {
        $user = User::findIdentity($id);
        if (!$user) {
            return $this->redirect('/user/index');
        }

        $query = UserMessage::find()->where(['user_id' => $id, 'date_delete' => null])->with('sender')->orderBy('id DESC');
        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('messages', [
            'models' => $models,
            'user' => $user,
            'pages' => $pages,
        ]);
    }

    public function actionMessage_add($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }
        
        $model = new UserMessage();
        if (Yii::$app->request->isPost AND $model->load(Yii::$app->request->post())) {
            $model->user_id = $id;
            $model->date_add = date('Y-m-d H:i:s');
            if($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Сообщение было успешно отправлено');
                return $this->redirect('/user/messages/' . $id);
            }
        }
        $accounts = Sender::find()->all();
        return $this->render('message', [
            'model' => $model,
            'user' => $user,
            'accounts' => $accounts,
        ]);
    }

    public function actionMessage($id)
    {
        if (!$model = UserMessage::findIdentity($id) OR !$user = User::findIdentity($model->user_id)) {
            return $this->redirect('/user/index');
        }

        if (Yii::$app->request->isPost AND $model->load(Yii::$app->request->post())) {
//            $model->user_id = $id;
//            $model->date_add = date('Y-m-d H:i:s');
            if($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Сообщение было успешно изменено');
                return $this->redirect('/user/messages/' . $user->id);
            }
        }
        $accounts = Sender::find()->all();
        return $this->render('message', [
            'model' => $model,
            'user' => $user,
            'accounts' => $accounts,
        ]);
    }


    public function actionMessage_delete($id)
    {
        if ($model = UserMessage::findIdentity($id)) {
            $user_id = $model->user_id;
            $model->date_delete = date('Y-m-d H:i:s');
            $model->save();
            Yii::$app->getSession()->setFlash('success', 'Сообщение было успешно удалено');
            return $this->redirect('/user/messages/' . $user_id);
        }
        return $this->redirect('/user/index');
    }

    public function actionVerification($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        $documents = UserDoc::findIdentityUserId($id);

        $query = UsersDocumentsUploaded::find()->where(['user_id' => $id])->orderBy('id DESC');
        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $uploaded = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('verification', [
            'documents' => $documents,
            'uploaded' => $uploaded,
            'user' => $user,
            'pages' => $pages,
        ]);
    }


    public function actionIplog($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        $query = UserIpLog::find()->where(['user_id' => $id])->orderBy('id DESC');
        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 30]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $logs = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('iplog', [
            'logs' => $logs,
            'user' => $user,
            'pages' => $pages,
        ]);
    }

    public function actionRemoveBonusDebt($id)
    {
        if (!$bonus_debt = BonusDebt::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        if ($bonus_debt->status == 1) {
            $bonus_debt->status = 3;
            $bonus_debt->save();
        }


        return $this->redirect('/user/' . $bonus_debt->user_id . '/balance-bonus');
    }

    public function actionBan_user($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        $user->banned = 1;
        $user->sms_confirm = false;
        $user->email_confirm = false;
        $user->save();

        Yii::$app->getSession()->setFlash('success', 'Пользователь успешно забанен');
        return $this->redirect('/user/' . $id);
    }

    public function actionUnban_user($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        $user->banned = false;
        $user->save();

        Yii::$app->getSession()->setFlash('success', 'Пользователь успешно разбанен');
        return $this->redirect('/user/' . $id);
    }

    public function actionVerificate($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        $user->verificate();
        $user->refresh();

        return $this->redirect('/user/verification/' . $user->id);
    }

    public function actionUnverificate($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        if ($user->verified) {
            $user->verified = false;
        }
        $user->save();
        return $this->redirect('/user/verification/' . $user->id);
    }


    public function actionDecline_documents($id)
    {
        if (!$user = User::findIdentity($id)) {
            return $this->redirect('/user/index');
        }

        ChatTemplate::sendMessageFromTemplate($id, 15);

        UserDoc::updateAll(['need_verification' => false], ['user_id' => $id]);
        return $this->redirect('/user/verification/' . $user->id);
    }

    function getPartners($user_id) {
        $data = [];
        foreach (User::find()->where(['partner_id' => $user_id])->all() as $child_partner) {
            $data[] = [
                'id' => $child_partner->id,
                'username' => $child_partner->username,
                'email' => $child_partner->email
            ];
            $data = array_merge($data, static::getPartners($child_partner->id));
        }
        return $data;
    }

    public function actionPartnerka($id)
    {
        $user = User::findIdentity($id);
        $message = '';

        if (!$user) {
            return $this->redirect('/user/index');
        }

        if (isset($_GET['partners_emails'])) {
            $data = static::getPartners($id);
            $title = ['id', 'Логин', 'email'];

            $export_name = $user->username.'_partners(' . date('d.m.Y') . ')';

            return Servis::getInstance()->export($data, $title, $export_name . '.csv');
        }

        if (Yii::$app->request->isPost AND $post = Yii::$app->request->post()) {
            $message = 'Данные сохранены';
            if ($_POST['status_in_partner'] != $user->status_in_partner) {
                $user->status_in_partner = $_POST['status_in_partner'];
                $user->save();

                LogMy::getInstance()->setLog(['method' => 'actionPartnerka через админку', 'status' => $user->status_in_partner, 'user' => $user->id . ' ' . $user->username], 'change_status');
            }
            if (!empty($_POST['delpartner'])) {
                $ids = explode('|', $_POST['delpartner']);
                $tmp = [];
                foreach ($ids as $id) {
                    if ($id) {
                        $tmp[] = $id;
                    }
                }
                User::delPartners($tmp);
            }
            if (!empty($_POST['addpartner']) && is_numeric($_POST['addpartner'])) {
                $res = UserPartnerInfo::addPartner($_POST['addpartner'], $user->id);
                if (!$res) {
                    $message = 'Нельзя быть партнером самому себе';
                }
               UserPartnerInfo::tree($user->id);
            }

        }

        $partner_info = UserPartnerInfo::findIdentityUserId($user->id);
        if (!$partner_info) {
            $partner_info = new UserPartnerInfo();
        }

        $ids = [];
        if($arr_line = unserialize($partner_info->arr_line)) {
            foreach ($arr_line as $key => $arr) {
                foreach ($arr as $child_id) {
                    $ids[] = $child_id;
                }
            }
        }



        $table = User::find()
            ->select(['*', 'IF(sm1 is null, 0, sm1) as difference', 'IF(sm2 is null, 0, sm2) as result', 'first_deposit_date', '(user.balance - if(sm3 IS NULL, 0, sm3) + if(sm4 IS NULL, 0, sm4)) as total_b'])
            ->where(['id' => $ids])
            ->leftJoin('(SELECT MIN(date_add) as first_deposit_date, user_id FROM balance_log WHERE status = 1 AND operation IN (0,3) GROUP BY user_id) as dep_date on dep_date.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status = 1 AND operation = 10 GROUP BY user_id) as bl1 on bl1.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm2, from_user_id FROM balance_partner_log WHERE user_id = ' . $user->id . ' AND status = 1  GROUP BY from_user_id) as bl2 on bl2.from_user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm3, user_id FROM balance_log WHERE status = 4 AND operation = 5 AND summ < 0 GROUP BY user_id) as bl3 on bl3.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm4, user_id FROM balance_log WHERE status = 4 AND operation = 5 AND summ > 0 GROUP BY user_id) as bl4 on bl4.user_id = user.id')
            ->all();


        return $this->render('partnerka', [
            'user' => $user,
            'partner_info' => $partner_info,
            'message' => $message,
            'table' => $table
        ]);
    }
    
    

    public function actionBalance($id)
    {
        $user = User::findIdentity($id);
        if (!$user) {
            return $this->redirect('/user/index');
        }

        if (Yii::$app->request->isPost AND $post = Yii::$app->request->post()) {

            if (isset($post['set-balanse-user']) && !empty($post['balance'])) {
                $user->balance = $user->balance + $post['balance'];
                $res = $user->save();

                if ($res) {
                    $comment = ($post['comment']) ? $post['comment'] : null;

                    $balanse_log = new BalanceLog();
                    $balanse_log->addLog($id, $post['balance'], 3, 1, BalanceLog::internal_transfer, null, $comment);
                    $balanse_log->save();

                    $user->giveFirstBonus();
                }
            }
        }

        return $this->render('balance', [
            'user' => $user,
        ]);
    }
    

    public function actionDocuments()
    {
        $query = UserDoc::find()->where('need_verification = 1')->with('user');

        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление

        return $this->render('documents', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionCardPaymentLog()
    {
        $query = PaymentCardRequest::find()->orderBy('status, date_add');

        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление

        return $this->render('card-payment-log', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionAprovecardpayment($id)
    {
        if (!$payment = PaymentCardRequest::findIdentity($id)) {
            return $this->redirect('/user/card-payment-log');
        }

        if ($payment->status == 1) {
            if ($payment->approveRequest()) {
                Yii::$app->session->setFlash('success', 'Запрос пополнения успешно подтвержден');
            } else {
                Yii::$app->session->setFlash('warning', 'Возникла ошибка');
            }
        }
        return $this->redirect('/user/card-payment-log');
    }

    public function actionDeclinecardpayment($id)
    {
        if (!$payment = PaymentCardRequest::findIdentity($id)) {
            return $this->redirect('/user/card-payment-log');
        }

        if ($payment->status == 1) {
            if ($payment->declineRequest()) {
                Yii::$app->session->setFlash('success', 'Запрос пополнения успешно отклонен');
            } else {
                Yii::$app->session->setFlash('warning', 'Возникла ошибка');
            }
        }
        return $this->redirect('/user/card-payment-log');
    }


    public function actionDocument($id)
    {
        $model = UserDoc::findIdentity($id);

        return $this->render('document', [
            'model' => $model,
        ]);
    }

    public function actionIdentify($id)
    {
        $document = UserDoc::findIdentity($id);
        $document->need_verification = false;
        $document->save();

        $user = User::findIdentity($document->user_id);
        $user->verified = true;
        $user->save();
        return $this->redirect('/user/documents');
    }
    


    public function actionObjectives($id)
    {
        $user = User::findIdentity($id);
        if (!$user) {
            return $this->redirect('/user/index');
        }
        $query = UserObjectives::find()->where(['user_id' => $id])->orderBy('date_end DESC');

        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('objectives', [
            'user' => $user,
            'models' => $models,
            'pages' => $pages,
        ]);
    }
}
