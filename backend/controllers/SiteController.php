<?php
namespace backend\controllers;

use backend\models\AddBonusLogForm;
use backend\models\BannerForm;
use backend\models\EmailForm;
use backend\models\SmsForm;
use common\models\BalanceBonusLog;
use common\models\BalanceLog;
use common\models\BalancePartnerLog;
use common\models\Banner;
use common\models\EmailTemplate;
use common\models\LoginForm;
use common\models\ManagerCard;
use common\models\Options;
use common\models\PartnerBaluLog;
use common\models\SmsManager;
use common\models\SmsTemplate;
use common\models\SmsLog;
use common\models\trade\TradingAccount;
use common\models\User;
use common\models\UserBonusRequest;
use common\models\VisitorLog;
use common\service\PartnerProgram;
use frontend\models\user\TransferForm;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use backend\models\ChangeTransferForm;
use common\models\UserPartnerInfo;
use common\models\Queue;
use common\service\Servis;
use common\models\LogConfirm;
use backend\models\InvestBonusForm;
use common\models\trade\Investment;
/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['login', 'error', 'logout'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['moderator', 'manager'],
                    ],
                    [
                        'actions' => [ 'change_cashout','money_log'],
                        'allow' => true,
                        'roles' => ['manager'],
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

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'image-upload' => [
                'class' => 'vova07\imperavi\actions\UploadFileAction',
                'url' => '//' . Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend') . '/web/upload/pages/' // Or absolute path to directory where files are stored.
            ],
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => '//' . Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend') . '/web/upload/pages/', // Or absolute path to directory where files are stored.
                'type' => '0',
            ],
            'files-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => '//' . Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend') . '/web/upload/pages/', // Or absolute path to directory where files are stored.
                'type' => '1',//GetAction::TYPE_FILES,
            ],
            'file-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => '//' . Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend') . '/web/upload/pages/' // Or absolute path to directory where files are stored.
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/login']);
        }
        if (!Yii::$app->user->can('manager') AND !Yii::$app->user->can('moderator')) {
            Yii::$app->user->logout();
            return $this->redirect(['/login']);
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {

//        if (!Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
        $this->layout = 'login';

        $client_ip = Yii::$app->request->userIP;
        $ips = explode(",", Options::getOptionValueByKey('login_admin_white_ip'));
        $open_gate = in_array($client_ip, $ips);
        
        $model = new LoginForm();
        if(!SmsManager::getActiveSmsProvider() OR $open_gate) {
            if ($model->load(Yii::$app->request->post()) && $model->login(true)) {

                return $this->goBack();
            } else {
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        } else {
            if ($model->load(Yii::$app->request->post()) ) {
                if ($model->validate()) {
                    if(!$user = User::findByUsername($model->username)) {
                        $model->addError('username', 'Пользователь не найден');
                        return $this->goBack();
                    }
                    if ($model->stage == 1 AND $user->validatePassword($model->password)) {
                        $model->stage = 2;

                        $phone = $model->phone;
                        $code = rand( 10000, 99999 );
                        $confirm = new LogConfirm();
                        $confirm->date_add = time();
                        $confirm->phone = $phone;
                        $confirm->code = $code;
                        $confirm->save();

                        if($mes = SmsManager::stopSpam($phone, 'Вход в админку')){
                            var_dump($mes);
                            die;
                        }

                        $res = SmsManager::sendOne(8, $phone, ['code' => $code]);
                        if(!$res){
                            var_dump('Неправильно введен номер телефона');
                            die;
                        }

                        return $this->render('login', [
                            'model' => $model,
                        ]);
                    } elseif($model->stage == 2 AND $model->validateCode()) {
                        if (!$model->login(true)) {
                            if(!Yii::$app->user->can('manager')) {
                                $model->addError('password', 'Пользователь не имеет прав админинстратора');
                                Yii::$app->user->logout();
                                return $this->goBack();
                            }
                            return $this->redirect('index');
                        } else {
                            return $this->goBack();
                        }
                    }
                    return $this->render('login', [
                        'model' => $model,
                    ]);
                }
                return $this->goBack();
            } else {
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        }

    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    //вывод всех смс шаблонов
    public function actionSms_template()
    {
        $user = Yii::$app->user->identity;
     //   $user->setForbidden();

        // выполняем запрос
        $query = SmsTemplate::find();

        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('sms-template', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    //смс Провайдеры
    public function actionSms_settings()
    {
        $query = SmsManager::find();

        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('sms-settings', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }


    public function actionActive_sms_provider($id)
    {
        $user = Yii::$app->user->identity;
      //  $user->setForbidden();

        $sms_manager = SmsManager::findIdentity($id);

        if (!$sms_manager) {
            $this->redirect(Url::to('sms_settings'));
        }

        SmsManager::updateAll(['is_active' => 0]);

        $sms_manager->is_active = 1;
        $sms_manager->save();

        return $this->redirect(Url::to('sms_settings'));
    }

    public function actionDeactive_sms_provider($id)
    {
        $user = Yii::$app->user->identity;
      //  $user->setForbidden();

        $sms_manager = SmsManager::findIdentity($id);

        if (!$sms_manager) {
            $this->redirect(Url::to('sms_settings'));
        }

        $sms_manager->is_active = 0;
        $sms_manager->save();

        return $this->redirect(Url::to('sms_settings'));
    }

    public function actionEdit_sms_provider($id)
    {
        $user = Yii::$app->user->identity;
     //   $user->setForbidden();

        $sms_manager = SmsManager::findIdentity($id);

        if (!$sms_manager) {
            $this->redirect(Url::to('sms_settings'));
        }


        if ($sms_manager->load(Yii::$app->request->post())) {

            if ($sms_manager->save()) {
                $this->redirect(Url::to('sms_settings'));
            }
        }

        return $this->render('sms-provider-single', [
            'model' => $sms_manager,
        ]);
    }

    public function actionChange_transfer($id)
    {

        $balance_log = BalanceLog::findIdentity($id);
        if (empty($balance_log)) {
            throw new \yii\web\ForbiddenHttpException('Указанный id не существует');
        }

        $model = new ChangeTransferForm();
        $model->setData($balance_log);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->change($balance_log)) {
                $this->redirect(Url::to('/site/money_log'));
            }
        }
        $helper = [];
        $helper['sender'] = User::findIdentity($balance_log->user_id);
        if (!empty($balance_log->recipient_user_id)) {
            $helper['recipient'] = User::findIdentity($balance_log->recipient_user_id);
        }

        return $this->render('change-transfer', [
            'model' => $model,
            'helper' => $helper,

        ]);
    }

    public function actionChange_cashout($id)
    {

        $balance_log = BalanceLog::findIdentity($id);
        if (empty($balance_log)) {
            throw new \yii\web\ForbiddenHttpException('Указанный id не существует');
        }

        $model = new ChangeTransferForm();
        $model->setData($balance_log);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->change($balance_log, true)) {
                $this->redirect(Url::to('/site/money_log'));
            }
        }
        $helper = [];
        $helper['sender'] = User::findIdentity($balance_log->user_id);
        if (!empty($balance_log->recipient_user_id)) {
            $helper['recipient'] = User::findIdentity($balance_log->recipient_user_id);
        }

        return $this->render('change-transfer', [
            'model' => $model,
            'helper' => $helper,

        ]);
    }

    public function actionMoney_log()
    {

        $user = Yii::$app->user->identity;
        //$user->setForbidden();

        $availables_operations = BalanceLog::getOperationNames();

        $search_query = array();//operation
        if (isset($_GET['status']) && $_GET['status'] >= 0) {
            $search_query['status'] = $_GET['status'];
        }

        if (isset($_GET['operation']) && $_GET['operation'] >= 0 AND key_exists( intval($_GET['operation']),$availables_operations)) {

//            if($_GET['operation'] == '0') {
//                $search_query['system'] = [1,2,3,4,6,7,8,9];
//                $search_query['operation'] = 0;
//            } else {
            $search_query['operation'] = $_GET['operation'];
//            }
        } elseif(!Yii::$app->user->can('admin')) {
            $key_array = [];
            foreach ($availables_operations as $key => $value) $key_array[] = $key;
            $search_query['operation'] = $key_array;
        }
        if (!empty($_GET['user'])) {
            $user = User::findByUsername($_GET['user']);
            if ($user) {
                $search_query['user_id'] = $user->id;
            }
        }
        $query = BalanceLog::find()->andWhere($search_query);

        if (!empty($_GET['date_from'])) {
            $query->andFilterWhere(['<=', 'date_add', $_GET['date_from']]);
        }
        if (!empty($_GET['date_to'])) {
            $query->andFilterWhere(['>=', 'date_add', $_GET['date_to']]);
        }

        $query->orderBy('id DESC');

        if (isset($_GET['export'])) {
            $res = $query->all();
            $title = ['id', 'id Пользователя', 'Логин', 'Дата', 'Сумма $', 'Платежная система', 'Операция', 'Статус', 'Комментарий'];

            $data = [];
            foreach ($res as $item) {
                $t = [];
                $t[] = $item->id;
                $t[] = $item->user_id;
                $t[] = $item->user->username;
                $t[] = $item->date_add;
                $t[] = $item->summ;
                $t[] = BalanceLog::$system[$item->system];
                $t[] = BalanceLog::$operation_name[$item->operation];
                $t[] = BalanceLog::$status_name[$item->status];
                $t[] = $item->comment;
                $data[] = $t;
            }

            $export_name = 'balance_operations';
            if (isset($_GET['operation'])) {
                if ($_GET['operation'] == '1') {
                    $export_name = 'withdraw(' . date('d.m.Y') . ')';
                } elseif ($_GET['operation'] == '0') {
                    $export_name = 'deposit(' . date('d.m.Y') . ')';
                }
            }

            Servis::getInstance()->export($data, $title, $export_name . '.csv');
        }

        $query_info_in = clone $query;
        $query_info_in = $query_info_in->andFilterWhere(['>', 'summ', 0]);
        $query_info_in = $query_info_in->sum('summ');

        $query_info_out = clone $query;
        $query_info_out = $query_info_out->andFilterWhere(['<', 'summ', 0]);
        $query_info_out = $query_info_out->sum('summ');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('balance-log', [
            'models' => $models,
            'pages' => $pages,
            'query_info_in' => $query_info_in,
            'query_info_out' => $query_info_out,
        ]);
    }

    public function actionBalanceLog()
    {
        $user = Yii::$app->user->identity;

        $order_by = 'user.id';
        if (!empty($_GET['order'])) {
            $order_by = $_GET['order'];

        }
        if (!empty($_GET['order_type'])) {
            $order_by .= ' ' . $_GET['order_type'];
        }
        
        $user_demo_accounts = Yii::$app->db->createCommand("SELECT id FROM trading_account WHERE type_account = 4")->queryAll();
        $demos = [];
        foreach ($user_demo_accounts as $d) {
            $demos[] = $d['id'];
        }

        $query = User::find()
            ->select(['user.id',
                'user.username',
                'user.balance',
                'IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0)  + IF(invested_today IS NOT NULL, invested_today, 0) as investments_summ',
                'user.balance_partner',
                'SUM(IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + user.balance + IF(invested_today IS NOT NULL, invested_today, 0)) AS total_b',
                'SUM(IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + IF(invested_today IS NOT NULL, invested_today, 0) + user.balance + user.balance_partner) AS total_b_with_p',
                'first_deposit_date',
                'IF(first_deposit_sum IS NOT NULL ,first_deposit_sum, 0) as first_deposit_sum',
                'IF(deposit_sum IS NOT NULL ,deposit_sum, 0) as deposit_sum',
                'IF(withdraw_sum IS NOT NULL ,withdraw_sum, 0) as withdraw_sum',
                'IF(difference IS NOT NULL ,difference, 0) as difference',
                'IF((difference - first_deposit_sum) IS NOT NULL ,(difference - first_deposit_sum), 0) as result',
                '(IF(investments_summ IS NOT NULL, investments_summ, 0) + IF(solutions_summ IS NOT NULL, solutions_summ, 0) + user.balance + IF(invested_today IS NOT NULL, invested_today, 0) - (IF(dtp2 IS NOT NULL ,dtp2, 0) + IF(dtp1 IS NOT NULL ,dtp1 , 0))) as dtp',
            ])
            ->leftJoin('(SELECT SUM(summ_current) AS investments_summ, SUM(summ_show) AS investments_show, investments.user_id
                 FROM investments
                 LEFT JOIN `trading_account` as t ON investments.trading_account_id = t.id 
                 WHERE investments.bonus_money IS NULL 
                 AND investments.deleted = false
                 AND (t.type_account <> 4 OR t.id IS NULL)  AND investments.solution_id IS NULL
                 GROUP BY investments.user_id) AS i
                 ON user.id = i.user_id ')
            ->leftJoin('(SELECT SUM(summ) as invested_today, ti.user_id FROM `traiding_investments_log` as ti
                LEFT JOIN investments as i on i.id = ti.investment_id
                WHERE i.date_add < CURDATE() AND type = 1 AND status = 2 AND datetime_add >= CURDATE()  AND  ti.solution_id is NULL
                GROUP BY ti.user_id) as inv_td ON inv_td.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ_current) AS solutions_summ, solutions.user_id
                 FROM investments as solutions
                 WHERE solutions.bonus_money IS NULL 
                 AND solutions.deleted = false
                 AND solutions.solution_id IS NOT NULL
                 GROUP BY solutions.user_id) AS i2
                 ON user.id = i2.user_id ')
            ->leftJoin('(SELECT balance_log.date_add as first_deposit_date, balance_log.summ as first_deposit_sum, balance_log.user_id FROM balance_log WHERE operation IN (0,3) AND balance_log.status = 1 GROUP BY balance_log.user_id ORDER BY balance_log.date_add ASC) as bl1 ON bl1.user_id = user.id')
            ->leftJoin('(SELECT SUM(balance_log.summ) as deposit_sum, balance_log.user_id FROM balance_log WHERE operation IN (0,3) AND system <> 5 AND balance_log.status = 1 GROUP BY balance_log.user_id) as bl2 
                ON bl2.user_id = user.id')
            ->leftJoin('(SELECT SUM(balance_log.summ) as withdraw_sum, balance_log.user_id FROM balance_log WHERE operation = 1 AND system <> 5 AND balance_log.status = 1 GROUP BY balance_log.user_id) as bl3 
                ON bl3.user_id = user.id')
            ->leftJoin('(SELECT SUM(balance_log.summ) as difference, balance_log.user_id FROM balance_log WHERE operation IN (0,1,3) AND system <> 5 AND balance_log.status = 1 GROUP BY balance_log.user_id) as bl4 
                ON bl4.user_id = user.id')
            ->leftJoin('(SELECT SUM(balance_log.summ) as dtp1, balance_log.user_id FROM balance_log WHERE operation IN (0,1,2,3,6) AND balance_log.status = 1 GROUP BY balance_log.user_id) as bl5 
                ON bl5.user_id = user.id')
            ->leftJoin('(SELECT SUM(balance_log.summ) as dtp2, balance_log.user_id FROM balance_log WHERE operation = 1 AND balance_log.status IN (0,3) GROUP BY balance_log.user_id) as bl6 
                ON bl6.user_id = user.id')
            ->where('user.status = 10')
            ->groupBy('user.id');
        
        if (!empty($_GET['user'])) {
            $query->andWhere('user.username LIKE "%' . $_GET['user'] . '%"');
        }
        if (!empty($_GET['manager_id'])) {
            $query->andWhere(['user.manager_card_id' => $_GET['manager_id']]);
        }

        $query->orderBy($order_by);

        if (isset($_GET['export'])) {
            $res = $query->all();
            $title = ['id', 'Логин', 'Баланс $', 'Сумма инвестиций', 'Сумма', 'Партнерский счет', 'Сумма с партнерским счетом', 'Дата первого пополнения', 'Первый ввод', 'Вводы', 'Выводы', 'Разница', 'Результат', 'Заработано'];
            $service = Servis::getInstance();
            $data = [];
            foreach ($res as $model) {
                $t = [];
                $t[] = $model->id;
                $t[] = $model->username;
                $t[] = $service->beautyDecimal($model->balance, 2, ',');
                $t[] = $model->investments_summ ? $service->beautyDecimal($model->investments_summ, 2, ',') : 0;
                $t[] = $service->beautyDecimal($model->total_b, 2, ',');
                $t[] = $service->beautyDecimal($model->balance_partner, 2, ',');
                $t[] = $service->beautyDecimal($model->total_b_with_p, 2, ',');
                $t[] = $model->first_deposit_date;
                $t[] = $service->beautyDecimal($model->first_deposit_sum, 2, ',');
                $t[] = $service->beautyDecimal($model->deposit_sum, 2, ',');
                $t[] = $service->beautyDecimal($model->withdraw_sum, 2, ',');
                $t[] = $service->beautyDecimal($model->difference, 2, ',');
                $t[] = $service->beautyDecimal($model->result, 2, ',');
                $t[] = $service->beautyDecimal($model->dtp, 2, ',');
                $data[] = $t;
            }

            $export_name = 'balance_log(' . date('d.m.Y') . ')';

            Servis::getInstance()->export($data, $title, $export_name . '.csv');
        }

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        // Передаем данные в представление

        $managers[0] = 'Все менеджеры';
        foreach (ManagerCard::getManagers() as $key => $value) {
            $managers[$key] = $value;
        }
        return $this->render('money-balance-log', [
            'models' => $models,
            'pages' => $pages,
            'managers' => $managers,
        ]);
    }


    public function actionBonusLog()
    {

        $user = Yii::$app->user->identity;

        $search_query = array();//operation

        if (!empty($_GET['user'])) {
            $user = User::findByUsername($_GET['user']);
            if ($user) {
                $search_query['user_id'] = $user->id;
            }
        }

        $query = BalanceBonusLog::find()->where($search_query);

        if (!empty($_GET['date_from'])) {
            $query->andFilterWhere(['<=', 'date_add', $_GET['date_from']]);
        }
        if (!empty($_GET['date_to'])) {
            $query->andFilterWhere(['>=', 'date_add', $_GET['date_to']]);
        }

        $query->orderBy('expired ASC, ISNULL(date_end) DESC,id DESC');

        $query_info_in = clone $query;
        $query_info_in = $query_info_in->andFilterWhere(['>', 'summ', 0]);
        $query_info_in = $query_info_in->sum('summ');

        $query_info_out = clone $query;
        $query_info_out = $query_info_out->andFilterWhere(['<', 'summ', 0]);
        $query_info_out = $query_info_out->sum('summ');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('bonus-log', [
            'models' => $models,
            'pages' => $pages,
            'query_info_in' => $query_info_in,
            'query_info_out' => $query_info_out,
        ]);
    }

    public function actionBonusAdd($id = null)
    {

        $user = Yii::$app->user->identity;

        $model = new AddBonusLogForm();
        if ($model->load(Yii::$app->request->post())) {
            if(User::findIdentity($model->user_id)) {
                if($model->add()){
                    $this->redirect(Url::to('/site/bonus-log'));
                }
            } else {
                $model->addError('user_id', 'Пользователь с таким id не существует');
            }


        } else if ($id) {
            $model->setData(BalanceBonusLog::findIdentity($id));
        }

        return $this->render('bonus-form', [
            'model' => $model,
        ]);
    }

    public function actionBonusInvest($id)
    {
        if(!$bonus = BalanceBonusLog::findIdentity($id)) {
            return $this->redirect(['site/bonus-log']);
        }
       
        $model = new InvestBonusForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->invest($bonus) AND !$model->getErrors()) {
                $bonus->refresh();
                Yii::$app->getSession()->setFlash('success', 'Инвестирование прошло успешно');
            }
        }

        return $this->render('bonus-invest', [
            'model' => $model,
            'bonus' => $bonus
        ]);
    }

    public function actionBallLog()
    {

        $user = Yii::$app->user->identity;

        $search_query = array();
        if (!empty($_GET['user'])) {
            $user = User::findByUsername($_GET['user']);
            if ($user) {
                $search_query['user_id'] = $user->id;
            }
        }

        $query = PartnerBaluLog::find()->where($search_query);

        if (!empty($_GET['date_from'])) {
            $query->andFilterWhere(['<=', 'date_add', $_GET['date_from']]);
        }
        if (!empty($_GET['date_to'])) {
            $query->andFilterWhere(['>=', 'date_add', $_GET['date_to']]);
        }


        $query->orderBy('id DESC');

        $query_info_in = clone $query;
        $query_info_in = $query_info_in->andFilterWhere(['>', 'ball', 0]);
        $query_info_in = $query_info_in->sum('ball');

        $query_info_out = clone $query;
        $query_info_out = $query_info_out->andFilterWhere(['<', 'ball', 0]);
        $query_info_out = $query_info_out->sum('ball');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('ball-log', [
            'models' => $models,
            'pages' => $pages,
            'query_info_in' => $query_info_in,
            'query_info_out' => $query_info_out,
        ]);
    }

    public function actionPartnerLog()
    {

        $user = Yii::$app->user->identity;
    
        $search_query = array();

        if (!empty($_GET['user'])) {
            $user = User::findByUsername($_GET['user']);
            if ($user) {
                $search_query['user_id'] = $user->id;
            }
        }
        
        $query = BalancePartnerLog::find()->where($search_query);

        if (!empty($_GET['date_from'])) {
            $query->andFilterWhere(['<=', 'date_add', $_GET['date_from']]);
        }
        if (!empty($_GET['date_to'])) {
            $query->andFilterWhere(['>=', 'date_add', $_GET['date_to']]);
        }


        $query->orderBy('id DESC');

        $query_info_in = clone $query;
        $query_info_in = $query_info_in->andFilterWhere(['>', 'summ', 0]);
        $query_info_in = $query_info_in->sum('summ');

        $query_info_out = clone $query;
        $query_info_out = $query_info_out->andFilterWhere(['<', 'summ', 0]);
        $query_info_out = $query_info_out->sum('summ');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('partner-log', [
            'models' => $models,
            'pages' => $pages,
            'query_info_in' => $query_info_in,
            'query_info_out' => $query_info_out,
        ]);
    }

    //смс лог
    public function actionSms_log()
    {

        $user = Yii::$app->user->identity;

        $search_query = array();
        if (!empty($_GET['phone'])) {
            $search_query['phone'] = $_GET['phone'];
        }
        if (!empty($_GET['user'])) {
            $user = User::findByUsername($_GET['user']);
            if ($user) {
                $search_query['user_id'] = $user->id;
            }
        }
        $query = SmsLog::find()->where($search_query)->orderBy('date_add DESC');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        $pages->pageSizeParam = false;
        
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy('id DESC')
            ->all();
        return $this->render('sms-log', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    //редактировани шаблона смс
    public function actionEdit_sms($id)
    {
        $user = Yii::$app->user->identity;
        $sms_template = SmsTemplate::findIdentity($id);

        if (!$sms_template) {
            $this->redirect(Url::to('/site/sms_template'));
        }

        $model = new SmsForm();
        $model->setData($sms_template);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->add(false, $sms_template)) {
                $this->redirect(Url::to('/site/sms_template'));
            }
        }

        return $this->render('sms-single', [
            'model' => $model,
            'seo' => ['title' => 'Редактировать sms шаблон',
            ],
        ]);
    }

    // добавить смс шаблон
    public function actionSms_add()
    {
        $user = Yii::$app->user->identity;
        $model = new SmsForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->add()) {
                $this->redirect(Url::to('/site/sms_template'));
            }
        }

        return $this->render('sms-single', [
            'model' => $model,
            'seo' => ['title' => 'Добавить sms шаблон'],
        ]);
    }

    //вывод шаблонов писем
    public function actionEmail_template()
    {
        $user = Yii::$app->user->identity;
        $query = EmailTemplate::find();


        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('email-template', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }
    
    public function actionEdit_email($id)
    {
        $user = Yii::$app->user->identity;
        $email_template = EmailTemplate::findIdentity($id);

        if (!$email_template) {
            $this->redirect(Url::to('/site/email_template'));
        }

        $model = new EmailForm();
        $model->setData($email_template);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->add(false, $email_template)) {
                $this->redirect(Url::to('/site/email_template'));
            }
        }

        return $this->render('email-single', [
            'model' => $model,
            'seo' => ['title' => 'Редактировать шаблон',
            ],
        ]);
    }

    //добавить шаблон письма
    public function actionEmail_add()
    {
        $user = Yii::$app->user->identity;

        $model = new EmailForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->add()) {
                $this->redirect(Url::to('/site/email_template'));
            }
        }

        return $this->render('email-single', [
            'model' => $model,
            'seo' => ['title' => 'Добавить шаблон'],
        ]);
    }

    public function actionBanners()
    {
        $user = Yii::$app->user->identity;
        $query = Banner::find();


        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('banners', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionVisitors()
    {
        $user = Yii::$app->user->identity;
        $query = VisitorLog::find()->where(['sms_confirmed' => 1])->orderBy('status ASC, id DESC');
        
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('visitors', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionConfirm_visit($id)
    {
        if(!$log = VisitorLog::find()->where(['id' => $id])->one()) {
            Yii::$app->getSession()->setFlash('error', 'Заявка на посещение не найдена');
            return $this->redirect('site/visitors');
        }
        $log->status = 1;
        $log->save();

        Yii::$app->getSession()->setFlash('success', 'Заявка на посещение обработана');
        return $this->redirect('/site/visitors');
    }

    public function actionAdd_banner()
    {
        $user = Yii::$app->user->identity;
        $model = new BannerForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->add()) {
                $this->redirect(Url::to('/site/banners'));
            }
        }

        return $this->render('banner-single', [
            'model' => $model,
            'seo' => ['title' => 'Добавить шаблон'],
        ]);
    }
    
    public function actionEdit_banner($id)
    {
        $user = Yii::$app->user->identity;
        $banner = Banner::findIdentity($id);

        if (!$banner) {
            $this->redirect(Url::to('/site/banners'));
        }

        $model = new BannerForm();

        $model->setData($banner);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->add(false, $banner)) {
                $this->redirect(Url::to('/site/banners'));
            }
        }

        return $this->render('banner-single', [
            'model' => $model,
            'seo' => ['title' => 'Редактировать шаблон',
            ],
        ]);
    }


    public function actionFindUsers()
    {

        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $search = $request->post('search');
            $data['status'] = 'Ok';
            if (!$data['users'] = User::findUsersForList($search)) {
                $data['status'] = 'Empty';
            }
            return json_encode($data);
        }
    }

    public function actionSevenBonus()
    {
        $query = UserBonusRequest::find()->with('user')->orderBy('status ASC, date_add DESC');
        
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 25]);
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        
        return $this->render('seven-bonus', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionSevenBonusApprove($id)
    {
        if($bonus = UserBonusRequest::findIdentity($id)) {
            if($bonus->approveRequest()) {
                Yii::$app->getSession()->setFlash('success', 'Заявка принята, пользователю начислен бонус +7%');
            }
        }
        return $this->redirect(['/site/seven-bonus']);
    }

    public function actionSevenBonusDecline($id)
    {
        if($bonus = UserBonusRequest::findIdentity($id)) {
            if($bonus->declineRequest()) {
                Yii::$app->getSession()->setFlash('success', 'Заявка отклонена');
            }
        }
        return $this->redirect(['/site/seven-bonus']);
    }
    
}
