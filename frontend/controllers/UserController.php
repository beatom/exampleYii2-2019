<?php

namespace frontend\controllers;

use common\models\BalanceLog;
use common\models\Country;
use common\models\Currencies;
use common\models\DaysLog;
use common\models\Events;
use common\models\Options;
use common\models\Notification;
use common\models\Overdraft;
use common\models\PaymentSystems;
use common\models\PaymentSystemsWithdraw;
use common\models\promo\PromoBanner;
use common\models\Sender;
use common\models\trade\TradingAccount;
use common\models\UserBonusRequest;
use common\models\UserDoc;
use common\models\UserMessage;
use common\models\UserObjectives;
use common\models\UserSocial;
use common\service\api_terminal\Advcash;
use common\service\api_terminal\Cryptonator;
use common\service\api_terminal\Megatransfer;
use common\service\api_terminal\Perfectmoney;
use common\service\api_terminal\Ultrapays;
use common\service\PartnerProgram;
use common\service\Servis;
use frontend\models\CreateInvestmentAccountDemoForm;
use frontend\models\CreateInvestmentAccountForm;
use frontend\models\CreateTrustManagementAccount;
use frontend\models\PaymentCardRequest_yandex;
use frontend\models\user\CashoutForm;
use frontend\models\user\SmsConfirmForm;
use function PHPSTORM_META\type;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\EditUserFrom;
use frontend\models\user\SecurityForm;
use common\models\City;
use yii\data\Pagination;
use common\models\LogConfirm;
use common\models\User;
use common\models\SmsManager;
use common\models\SmsTemplate;
use frontend\models\user\TransferForm;
use common\service\api_terminal\Payeer;
use frontend\models\user\InvestForm;
use common\models\EmailTemplate;
use common\models\UserPartnerInfo;
use common\service\api_terminal\Interkassa;
use common\models\PaymentCardRequest;
use yii\web\UploadedFile;
use yii\web\Cookie;
use common\models\QueueMail;
use common\service\api_terminal\Bankcomat;
use common\models\PaymentLog;
use common\service\api_terminal\Piastrix;
use common\models\ChatMessage;
use common\models\ChatMessageMark;
use common\models\BalancePartnerLog;
/**
 * Site controller
 */
class UserController extends Controller
{
    public $layout = 'cabinet';
    public $menuPage = false;


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'my-info'],
                'rules' => [
                    [
                        'actions' => ['my-info', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    public function beforeAction($event)
    {
        $cookies = Yii::$app->request->cookies;
        if (Yii::$app->request->isAjax AND $cookies->has('pref_lang')) {
            Yii::$app->language = $cookies->getValue('pref_lang');
        } else {
            if ($cookies->has('pref_lang') AND !Yii::$app->request->getPreferredLanguage()) {
                Yii::$app->language = $cookies->getValue('pref_lang');
            } else {
                Yii::$app->response->cookies->add(new Cookie([
                    'name' => 'pref_lang',
                    'httpOnly' => false,
                    'value' => Yii::$app->language,
                ]));
            }
        }

        if (Yii::$app->user->isGuest) {
            $this->redirect(Url::to(['/site/login']));
            return false;
        }

        if (Yii::$app->user->identity->banned) {
            Yii::$app->user->logout();
            $this->redirect(Url::to(['/site/login']));
            return false;
        }

        return parent::beforeAction($event);
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->view->params['highlight-menu']['investor-cabinet'] = true;

        $user = Yii::$app->user->identity;
        $objective = $user->getActiveObjective();
        $user_balance = $user->getBalance();
        $model = new UserObjectives();

        $show_model = ($objective) ? false : true;
        $need_balance_error = false;
        if ($model->load(Yii::$app->request->post())) {
            if ($user_balance < 0.01) {
                $need_balance_error = true;
            } else {
                $show_model = true;
                $model->image_file = UploadedFile::getInstance($model, 'image_file');

                if ($objective = $model->saveObjective()) {
                    $show_model = false;
                    $objective = $user->getActiveObjective();
                    $model = new UserObjectives();
                }
            }
        }

        $current_using_summ = $user->balance < 3 ? 0 : $user->balance * (Events::getCurrentBankPercent() / 100);
        $total_profit = $user->getProfit(); //

        $last_day_result = 0;
        $h = date('H');
        if($h >= 10 AND $h < 15) {
            $last_day_result = BalanceLog::find()
                ->where(['user_id' => $user->id, 'status' => 1, 'operation' => 5])
                ->andWhere('date_add BETWEEN "' . date('Y-m-d 10:00:00', strtotime(' -1 day')) . '" AND "' . date('Y-m-d 15:00:00') . '"')
                ->sum('summ');
        } else {

            $last_day_result = Servis::getInstance()->beautyDecimal(($user->balance * (DaysLog::getPeriod()->getCurrentProfit() / 100)), 2);
        }
        $arriving_sum = BalanceLog::find()->where(['user_id' => $user->id, 'status' => 4])->andWhere('summ > 0')->sum('summ') - BalanceLog::find()->where(['user_id' => $user->id, 'status' => 4])->andWhere('summ < 0')->sum('summ');
        if(isset($_GET['clear'])) {
            Yii::$app->cache->delete('statistic_cabinet');
        }
        $data = Yii::$app->cache->getOrSet('statistic_cabinet', function () {
            return DaysLog::getTable();
        });
        
        return $this->render('index', [
            'data' => $data,
            'objective' => $objective,
            'user' => $user,
            'arriving_sum' => $arriving_sum,
            'total_profit' => $total_profit,
            'last_day_result' => $last_day_result,
            'current_using_summ' => $current_using_summ,
            'show_model' => $show_model,
            'model' => $model,
            'need_balance_error' => $need_balance_error,
        ]);
    }


    public function actionBets()
    {
        if (date('H') < 10) {
            $date = date('Y-m-d', strtotime('yesterday'));
        } else {
            $date = date('Y-m-d');
        }
        $day_log = DaysLog::getLog($date);

        $h = date('H');
        if ($h < 15 AND $h > 10) {
            $events = [];
        } else {
            $events = $day_log->events_complete;
        }

        $options = Options::getOptions(['bets_sender_id', 'bets_sender_message']);
        $sender = Sender::findIdentity($options[0]->value);
        $message = $options[1]->value;

        $updated_at = Yii::$app->request->cookies->getValue('bets_update');
        $updated_at = $updated_at ? $updated_at : date('Y-m-d 10:00:00', strtotime(' -2 days'));
        Yii::$app->response->cookies->add(new Cookie([
            'name' => 'bets_update',
            'value' => date('Y-m-d H:i:s'),
        ]));
        
        $user = Yii::$app->user->identity;
        $user->events_notice = false;
        $user->events_complete = false;
        $user->save();

        return $this->render('bets', [
            'message' => $message,
            'sender' => $sender,
            'day' => $day_log,
            'events' => $events,
            'updated_at' => $updated_at
        ]);
    }

    public function actionSettings()
    {
        $user = Yii::$app->user->identity;

        $user->BreackDate();
        $social = UserSocial::findIdentityUserId($user->id);

        $showmsg = false;

        $model = new EditUserFrom();
        $model->getSelectValue($user, $social);

        if ($model->load(Yii::$app->request->post())) {

            $model->phone = $user->phone ? $user->phone : $model->phone;
            $model->sms_code = '1';
            $model->payment_system = '1';
            $model->payment_address = '11111';
            $model->pasport_1 = UploadedFile::getInstance($model, 'pasport_1');
            $model->avatar = UploadedFile::getInstance($model, 'avatar');
            $model->phone = $user->phone;

            $allOk = true;
            if ((isset($model->pasport_1) AND $model->pasport_1 != '')) {
                if (!isset($model->city_name) OR $model->city_name == '' OR $model->city_name == null) {
                    $model->addError('city_name', 'Необходимо заполнить');
                    $allOk = false;
                }

                if (!isset($model->firstname) OR $model->firstname == '' OR $model->firstname == null) {
                    $model->addError('firstname', 'Необходимо заполнить');
                    $allOk = false;
                }

                if (!isset($model->lastname) OR $model->lastname == '' OR $model->lastname == null) {
                    $model->addError('lastname', 'Необходимо заполнить');
                    $allOk = false;
                }
                if (!isset($model->middlename) OR $model->middlename == '' OR $model->middlename == null) {
                    $model->addError('middlename', 'Необходимо заполнить');
                    $allOk = false;
                }
            }

            $model->phone = User::clearPhone($model->phone);
            if (isset($model->phone) AND $same_phone_users = User::find()->where(['phone' => $model->phone])->andWhere('id <> ' . $user->id)->all()) {
                $model->addError('phone', 'Этот номер телефона уже закреплен за другим пользователем');
                $allOk = false;
            }

            if ($allOk) {
                $model->firstname = htmlentities($model->firstname);
                $model->lastname = htmlentities($model->lastname);
                $model->middlename = htmlentities($model->middlename);
                $model->city_name = htmlentities($model->city_name);
            }

            if ($allOk AND $model->validate()) {

                $updateuser = $model->saveChange($user, $social);
                $user = $updateuser['user'];
                $social = $updateuser['social'];
                $showmsg = $updateuser['showmsg'];
            }
            $model->sms_code = null;
            $model->payment_system = null;
            $model->payment_address = null;
        }


        $countries = Country::find()->all();
        $location['country'] = Country::find()->where(['id' => $model->country_id])->asArray()->one();

        $user_doc = UserDoc::findIdentityUserId($user->id);

        if (!preg_match("/^[a-z0-9_-]+$/i", $user->username)) {
            $user->edit_username = 1;
            $user->save();
            $model->addError('username', 'Ник должен состоять только из латинских букв и/или цифр');
        }

        $security = new SecurityForm();
        if (isset($_POST['SecurityForm'])) {
            if ($security->load(Yii::$app->request->post()) AND $security->save($user)) {
                $showmsg = Yii::t('app', 'Пароль успешно изменен');
                $security->step = 1;
            }
        }
        return $this->render('settings', ['showmsg' => $showmsg,
            'model' => $model,
            'countries' => $countries,
            'user' => $user,
            'social' => $social,
            'user_doc' => $user_doc,
//            'ip_log' => $ip_log,
            'location' => $location,
            'secuity' => $security,
        ]);
    }
    
    public
    function actionFindCountry()
    {
        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $search = $request->post('search');
            $data['status'] = 'Ok';
            if (!$data['countries'] = Country::findCountry($search)) {
                $data['status'] = 'Empty';
            }
            return json_encode($data);
        }
    }


    public
    function actionPhoneConfirm()
    {
        $user = Yii::$app->user->identity;
        if (!SmsManager::getActiveSmsProvider()) {
            $user->sms_confirm = true; //на время отключения смс провайдеров
            $user->save();
        }

        if ($user->sms_confirm) {
            $this->redirect('/user/settings');
        }

        $model = new SmsConfirmForm();
        if ($mess = SmsManager::stopSpam($user->phone, 'Подтверждение телефона в настройках ЛК')) {
            $model->addError('sms_code', $mess);
            return $this->render('phone-confirm', [
                'model' => $model,
            ]);
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->checkCode($user, 'sms_confirm')) {
                return $this->redirect('/user/settings');
            }
        } else {


            $phone = User::clearPhone($user->phone);
            $code = rand(10000, 99999);
            $confirm = new LogConfirm();
            $confirm->date_add = time();
            $confirm->phone = $phone;
            $confirm->code = $code;
            $confirm->save();
            SmsManager::sendOne(SmsTemplate::templatePhoneConfirm, $phone, ['code' => $code], $user->id);
        }

        return $this->render('phone-confirm', [
            'model' => $model,
        ]);
    }

    public
    function actionEmailConfirm()
    {
        if (Yii::$app->request->isAjax) {

            $user = Yii::$app->user->identity;
            $code = Servis::getInstance()->randomCode(36);
            $confirm = new LogConfirm();
            $confirm->date_add = time();
            $confirm->email = $user->email;
            $confirm->code = $code;
            $confirm->save();
            $link = Url::home(true) . 'emailconfirm?code=' . $code;
            $data['link'] = '<a href="' . $link . '" target="_blank">' . $link . '</a>';
            $email_template = EmailTemplate::findIdentity(EmailTemplate::EMAIL_CONFIRM);
            $res = $email_template->getEmailTemplate($data);
//            $email_template->sendMail($user->email, $res['title'], $res);

            QueueMail::addTask(Yii::$app->params['adminEmail'],
                $user->email,
                $res['title'],
                EmailTemplate::EMAIL_CONFIRM,
                $data);
            return true;
        }
    }

    public
    function actionDeposit()
    {
        $user = Yii::$app->user->identity;
        $model = new InvestForm();
        $stage = 1;
        $payment_systems = PaymentSystems::getSystems(true, true);
        $form = false;
        if ($model->load(Yii::$app->request->post())) {
            $stage = 1;
            $form = $model->deposit();
            if(is_array($form) AND !$form['is_form']) {
                return $this->redirect($form['link']);
            }
        }
        $highlighted_system_id = $model->system_id ? $model->system_id : false;
        $systems = [];
        foreach ($payment_systems as $system) {
            if (!$highlighted_system_id) {
                $highlighted_system_id = $system['id'];
            }
            $systems[$system['id']] = $system;
        }
        $withdraws = PaymentSystemsWithdraw::getSystems(true, true);

        $outModel = new CashoutForm();
        $outModel->type = $user->payment_system;
        $outModel->account_number = $user->payment_address;

        $withdraw_method_selected = false;
        $first_withdraw = false;


        if($outModel->type AND $outModel->account_number AND PaymentSystemsWithdraw::findActive($outModel->type)) {
            $withdraw_method_selected = true;
        }

        if ($outModel->load(Yii::$app->request->post())) {
            $h = date('H');
            if($h >= 10 AND $h < 15) {
                $stage = 2;
                if($outModel->checkTransfer( $user )) {
                    $this->redirect(['/user/history']);
                }
            }
        }



        return $this->render('deposit', [
            'user' => $user,
            'systems' => $systems,
            'withdraws' => $withdraws,
            'model' => $model,
            'outModel' => $outModel,
            'stage' => $stage,
            'highlighted_system_id' => $highlighted_system_id,
            'send_form' => $form,
            'withdraw_method_selected' => $withdraw_method_selected,
        ]);
    }

    public function actionCashIn()
    {
        $payeer = null;
        $user = Yii::$app->user->identity;
        $model = new PaymentCardRequest();
        $model_yandex = new PaymentCardRequest_yandex();
        $usdRate = Currencies::getRate('USD');

        $user->BreackDate();
        $social = UserSocial::findIdentityUserId($user->id);

        $model_user = new EditUserFrom();
        $model_user->getSelectValue($user, $social);
        $showmsg = false;

        if ($model_user->load(Yii::$app->request->post())) {
            $model_user->phone = $user->phone ? $user->phone : $model_user->phone;
            $model_user->email = $user->email ? $user->email : $model_user->email;
            $model_user->pasport_1 = UploadedFile::getInstance($model_user, 'pasport_1');
            $model_user->pasport_2 = UploadedFile::getInstance($model_user, 'pasport_2');

            $model_user->phone = User::clearPhone($model_user->phone);
            $model_user->avatar = false;
            $model_user->firstname = htmlentities($model_user->firstname);
            $model_user->lastname = htmlentities($model_user->lastname);
            $model_user->middlename = htmlentities($model_user->middlename);
            $model_user->city_name = htmlentities($model_user->city_name);


            if ($model_user->validate()) {
                $updateuser = $model_user->saveChange($user, $social);
                $user = $updateuser['user'];
                $showmsg = $updateuser['showmsg'];
            }
        }

        if (!empty($_POST['summ']) && is_numeric($_POST['summ'])) {
            if ($_POST['payment-system'] == 'payee') {
                $payeer = Payeer::getInstance()->getForm($user, $_POST['summ']);
            }
        }


        return $this->render('cash-in', [
            'usdRate' => $usdRate,
            'model' => $model,
            'payeer' => $payeer,
            'user' => $user,
            'showmsg' => $showmsg,
            'model_yandex' => $model_yandex,
            'model_user' => $model_user,
            'cardnumber' => isset($_GET['order']) ? $_GET['order'] : false
        ]);
    }


    public function actionCashOut()
    {
        $user = Yii::$app->user->identity;
        $model = new CashoutForm();
        $flag = false;
        $overdraft_dolg = Overdraft::is_dolg($user->id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->checkTransfer($user)) {
                $flag = true;
            }
//            echo '<pre>'; var_dump($model); echo '</pre>';
        }

        if (SmsManager::getActiveSmsProvider()) {
            $sms_active = true;
        } else {
            $sms_active = false;
        }

        return $this->render('cash-out', [
            'user' => $user,
            'model' => $model,
            'flag' => $flag,
            'sms' => ($sms_active AND $user->sms_code_money) ? true : false,
            'user_id' => $user->id,
            'overdraft_dolg' => $overdraft_dolg,
        ]);
    }


    /**
     * перевод средств
     */
    public function actionTransfer()
    {
        return $this->redirect(['user/index']);
        $user = Yii::$app->user->identity;
        $model = new TransferForm();
        $recipient_user = false;
        if ($model->load(Yii::$app->request->post())) {

            if ($model->step == 3 && $model->transfer($user)) {
                return $this->redirect(Url::to(['/user/transfer-history']));
            } else if (($recipient_user = $model->checkTransfer($user)) && $model->step == 2) {
                $model->phone = $user->phone;
                $model->sms_code = '';
                $model->step = 3;
            }

            if ($model->errormy) {
                $model->addError('sms_code', $model->errormy);
            }
        }
        $overdraft_dolg = Overdraft::is_dolg($user->id);
        return $this->render('transfer', [
            'model' => $model,
            'recipient_user' => $recipient_user,
            'user' => $user,
            'overdraft_dolg' => $overdraft_dolg,
        ]);
    }

    public function actionHistory()
    {
        $user = Yii::$app->user->identity;
        //готовим запрос
        $my_query = ' id DESC';
        $pageSize = 10;

        // выполняем запрос
        $query = BalanceLog::find()->orderBy($my_query);
        $query->where(['user_id' => $user->id]);

        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('history', [
            'models' => $models,
            'pages' => $pages,
            'pageSize' => $pageSize
        ]);
    }

    public function actionMessages()
    {
        $user = Yii::$app->user->identity;
        //готовим запрос
        $my_query = ' id DESC';
        $pageSize = 7;

        // выполняем запрос
        $query = UserMessage::find()->orderBy('id DESC');
        $query->where(['user_id' => $user->id])->andWhere('date_delete IS NULL');

        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('messages', [
            'models' => $models,
            'pages' => $pages,
            'pageSize' => $pageSize
        ]);
    }

    public function actionPartner()
    {
        $this->layout = 'cabinet-partner';
        $user = Yii::$app->user->identity;
        $user_partner_info = UserPartnerInfo::findIdentityUserId($user->id);

        if (!$user_partner_info) {
            $user_partner_info = new UserPartnerInfo();
            $user_partner_info->user_id = $user->id;
            $user_partner_info->save();
        }

        if (!empty($user->partner_id)) {
            $partner = User::findIdentity($user->partner_id);
            $social_partner = UserSocial::findIdentityUserId($partner->id);
        } else {
            $partner = null;
            $social_partner = null;
        }
        $helper = array();
        $helper['bonus'] = UserPartnerInfo::getInfoBonusMounth($user->id);
        $helper['exchange_rate'] = explode('|', Options::getOptionValueByKey('exchange_rate'));

        $helper['notification']['new'] = Notification::getNewNotification($user->id);
        $helper['notification']['old'] = Notification::getOldNotification($user->id, 5);

//        echo '<pre>'; var_dump($helper); echo '<pre>';

        PartnerProgram::isChangeStatus($user, $user_partner_info);

        return $this->render('partner', [
            'partner' => $partner,
            'social_partner' => $social_partner,
            'user' => $user,
            'helper' => $helper,
            'user_partner_info' => $user_partner_info,
        ]);
    }

    public function actionCashback()
    {
        $user = Yii::$app->user->identity;

        $partner_info = UserPartnerInfo::tree($user->id);
        PartnerProgram::isChangeStatus($user);

        $ids = [];
        foreach (unserialize($partner_info->arr_line) as $key => $arr) {
            if ($user->status_in_partner + 1 < $key) {
                break;
            }
            foreach ($arr as $child_id) {
                $ids[] = $child_id;
            }
        }

        $pageSize = 10;

        // выполняем запрос
        $query = User::find()
            ->select(['*', 'IF(sm1 is null, 0, sm1) as difference', 'IF(sm2 is null, 0, sm2) as result', 'first_deposit_date'])
            ->where(['id' => $ids])
            ->leftJoin('(SELECT MIN(date_add) as first_deposit_date, user_id FROM balance_log WHERE status = 1 AND operation IN (0,3) GROUP BY user_id) as dep_date on dep_date.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status = 1 AND operation = 10 GROUP BY user_id) as bl1 on bl1.user_id = user.id')
            ->leftJoin('(SELECT SUM(summ) as sm2, from_user_id FROM balance_partner_log WHERE user_id = ' . $user->id . ' AND status = 1  GROUP BY from_user_id) as bl2 on bl2.from_user_id = user.id');
        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $pageSize]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $table = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('cashback', [
            'table' => $table,
            'user' => $user,
            'pages' => $pages,
            'pageSize' => $pageSize
        ]);
    }


    public function actionGetCardPaymentSumm()
    {
        if (Yii::$app->request->isAjax) {
            if (!$summ_usd = Yii::$app->request->post('summ_usd')) {
                return false;
            }
            $cur_usd = Currencies::getRate('usd');
            $step = 1;
            $max_minus = 10;
            $minus_value = 0;
            $summ = number_format($cur_usd * $summ_usd, 2, '.', '');
            $summ_start = $summ;
            $need_fix = true;

            do {
                if (!PaymentCardRequest::find()->where(['status' => 1, 'summ_rub' => $summ])->exists()) {
                    $need_fix = false;
                } else {
                    if ($minus_value <= $max_minus) {
                        $minus_value += $step;
                        $summ -= $step;
                    } else {
                        $summ = $summ_start;
                        $step = number_format($step / 2, 2, '.', '');
                        $minus_value = $step;
                        $summ -= $step;
                    }
                }

            } while ($need_fix);


            $data['summ'] = number_format($summ, 2, '.', '');
            $data['status'] = 'OK';
            return json_encode($data);
        }
    }


    public function actionPromo()
    {
        $user = Yii::$app->user->identity;
        $materials = PromoBanner::getMaterialsNew();

        return $this->render('promo', [
            'user' => $user,
            'materials' => $materials,
        ]);
    }
    
    public  function actionBankcomatPay()
    {
        if (Yii::$app->request->isAjax) {
            $order = PaymentLog::find()->where(['user_id' => Yii::$app->user->id, 'completed' => 0, 'payment_system' => BalanceLog::bankcomat])->orderBy('id DESC')->one();
            $order->to_execute = 1;
            $order->to_execute_time = date('Y-m-d H:i:s', strtotime(' +3 minutes'));
            $order->save();
            return true;
        }
    }

    public function actionReadMessage()
    {
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post()['id'];
            UserMessage::updateAll(['status' => 1], ['id' => $id, 'user_id' => Yii::$app->user->id]);
            return true;
        }
    }

    public function actionGetBets()
    {
        if (Yii::$app->request->isAjax) {

            $data['new_bets'] = false;
            $user = Yii::$app->user->identity;
            if($user->events_complete) {
                $data['new_bets'] = 'Новый результат';
            } elseif($user->events_notice) {
                $data['new_bets'] = 'Новое событие!';
            }
            $data['new_messages'] = $user->countUnreadMessages();
            return json_encode($data);
        }
    }

    public function actionChangePhoneSms()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            if(!$user->phone AND $new_phone = Yii::$app->request->post()['new_phone']) {
                $user->phone = $new_phone;
            }

            $data['message'] = '';
            $data['status'] = 0;
            if (!SmsManager::getActiveSmsProvider()) {
                $data['status'] = 2;
                $data['message'] = 'Изменение телефона недоступно';
                return json_encode($data);
            }

            if ($mess = SmsManager::stopSpam($user->phone, 'Изменение телефона в настройках ЛК')) {
                $data['status'] = 2;
                $data['message'] = $mess;
                return json_encode($data);
            }

            $phone = User::clearPhone($user->phone);
            $code = rand(10000, 99999);
            $confirm = new LogConfirm();
            $confirm->date_add = time();
            $confirm->phone = $phone;
            $confirm->code = $code;
            $confirm->save();
            if (SmsManager::sendOne(SmsTemplate::templatePhoneConfirm, $phone, ['code' => $code], $user->id)) {
                $data['status'] = 1;
            }

            return json_encode($data);
        }
    }

    public function actionChangePhoneConfirm()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $phone = Yii::$app->request->post('phone');
            $sms_code = Yii::$app->request->post('sms_code');

            if(!$user->phone AND $phone) {
                $user->phone = $phone;
            }

            if (!$sms_code OR !$phone) {
                return false;
            }
            $data['message'] = '';
            $data['status'] = 0;

            $check_phone = User::clearPhone($user->phone);
            if (!$confirm = LogConfirm::find()->orderBy(' date_add DESC')->where('code = "' . $sms_code . '" AND phone = ' . $check_phone . ' AND date_add > ' . (time() - 3600))->limit(1)->one()) {
                $data['message'] = 'Неправильный код';
                $data['status'] = 0;
                return json_encode($data);
            }
            $user->phone = User::clearPhone($phone);
            $user->save();
            $data['message'] = 'Телефон успешно изменен';
            $data['status'] = 1;

            return json_encode($data);
        }
    }

    public function actionChangePaymentSms()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $data['message'] = '';
            $data['status'] = 0;
            if (!SmsManager::getActiveSmsProvider()) {
                $data['status'] = 2;
                $data['message'] = 'Изменение реквизитов сейчас невозможно';
                return json_encode($data);
            }

            if ($mess = SmsManager::stopSpam($user->phone, 'Изменение платежной системы в настройках ЛК')) {
                $data['status'] = 2;
                $data['message'] = $mess;
                return json_encode($data);
            }

            $phone = User::clearPhone($user->phone);
            $code = rand(10000, 99999);
            $confirm = new LogConfirm();
            $confirm->date_add = time();
            $confirm->phone = $phone;
            $confirm->code = $code;
            $confirm->save();
            if (SmsManager::sendOne(SmsTemplate::templateChangePaymentSystem, $phone, ['code' => $code], $user->id)) {
                $data['status'] = 1;
            }

            return json_encode($data);
        }
    }

    public function actionChangePaymentConfirm()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $system_id = Yii::$app->request->post('system_id');
            $address = trim(Yii::$app->request->post('address'));
            $sms_code = Yii::$app->request->post('sms_code');

            if (!$sms_code OR !$address OR !$system_id OR !isset(BalanceLog::getPaymentsSystem()[$system_id])) {
                $data['message'] = 'Неправильный код';
                $data['status'] = 0;
                return json_encode($data);
            }
            $data['message'] = '';
            $data['status'] = 0;

            $check_phone = User::clearPhone($user->phone);
            if (!$confirm = LogConfirm::find()->orderBy(' date_add DESC')->where('code = "' . $sms_code . '" AND phone = ' . $check_phone . ' AND date_add > ' . (time() - 3600))->limit(1)->one()) {
                $data['message'] = 'Неправильный код';
                $data['status'] = 0;
                return json_encode($data);
            }
            $user->payment_system = $system_id;
            $user->payment_address = $address;
            $user->save();
            $data['message'] = 'Платежные реквизиты успешно изменены';
            $data['status'] = 1;

            return json_encode($data);
        }
    }

    public function actionDeleteObjective()
    {
        if (Yii::$app->request->isAjax) {
            return UserObjectives::updateAll(['date_end' => date('Y-m-d H:i:s')], ['user_id' => Yii::$app->user->id, 'date_end' => null]);
        }
    }


    public function actionSendChatMessage()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $parent_id = Yii::$app->request->post('parent_id');
            $text = trim(Yii::$app->request->post('message'));

            if (!$text OR $text == '') {
               return false;
            }
            ChatMessage::sendMessage($user->id, $text, $parent_id);
        }
    }

    public function actionMarkChatMessage()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $like = Yii::$app->request->post('like');
            $message_id = trim(Yii::$app->request->post('message_id'));
            $like = $like == 'true' ? true : false;
            if (!$message_id) {
                return false;
            }
            ChatMessageMark::mark($message_id, $user->id, $like);
        }
    }

    public function actionWithdrawBalancePartner()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $data['message'] = '';
            $data['status'] = 1;
            if($user->balance_partner < 0.01) {
                $data['message'] = 'Для вывода средств на балансе должно быть не меньшь 0.01$';
            } else {
                $data['status'] = 2;
               
                $balu_log = new BalancePartnerLog();
                $balu_log->add(($user->balance_partner * (-1)), $user->id,'вывод c партнерского счета на основной');
                BalanceLog::add($user->id, $user->balance_partner, 7,4,0, null, 'вывод  c партнерского счета на основной');
                $user->balance_partner = 0;
                $user->save();
                $data['message'] = 'Средства поступят в 15-00 на Ваш баланс.';
            }
            return json_encode($data);
        }
    }

    public function actionFirstBannerShown()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $user->first_banner_shown = 1;
            $user->save();
        }
    }


    public function actionGetSevenBonus()
    {
        if (Yii::$app->request->isAjax) {
            $user = Yii::$app->user->identity;
            $vk = trim(Yii::$app->request->post('vk'));
            $instagram = trim(Yii::$app->request->post('instagram'));
            $data['status'] = 1;
            if(!UserBonusRequest::checkOpen($user->id)) {
                $request = new UserBonusRequest();
                $request->user_id = $user->id;
                $request->vk = $vk;
                $request->instagram = $instagram;
                $request->status = 1;
                if($request->save()) {
                    $data['status'] = 2;
                }
            } 
            return json_encode($data);
        }
    }

}