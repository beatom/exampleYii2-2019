<?php
namespace frontend\controllers;

use common\models\AmoQueue;
use common\models\DaysLog;
use common\models\NewsUserRed;
use common\models\Page;
use common\models\Options;
use common\models\SmsTemplate;
use common\models\trade\TradingAccount;
use common\models\User;
use common\models\UserIpLog;
use common\service\api_terminal\Piastrix;
use common\service\Servis;
use common\widgets\ChatWidget;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\site\SignupFormStep2Form;
use frontend\models\ContactForm;
use common\models\LogConfirm;
use common\models\SmsManager;
use common\models\investments\InvestmentsPlan;
use common\service\LogMy;
use yii\db\Query;
use common\models\trade\Solution;
use yii\web\Cookie;
use common\models\trade\ManagerReviews;
use common\service\api_terminal\Bankcomat;
use common\models\SmsBlock;
use common\models\VisitorLog;
use common\models\trade\SolutionReviews;
use common\models\ChatMessage;
use common\models\SmsLog;
/**
 * Site controller
 */
class SiteController extends Controller
{
    public $layout = 'main';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function () {
                    return $this->goHome();
                }
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
                ],
            ],
        ];
    }

    public function beforeAction($event)
    {
//        if(!Yii::$app->user->isGuest) {
//            if(Yii::$app->user->identity->banned ) {
//                Yii::$app->user->logout();
//                $this->redirect(Url::to(['/site/login']));
//                return false;
//            }
//        }

//        if (!Yii::$app->request->cookies->has('user_authenticate') OR @!Yii::$app->user->identity->popup_banner_shown) {
//            if (!Yii::$app->user->isGuest) {
//                $user = Yii::$app->user->identity;
//                $user->popup_banner_shown = false;
//                $user->save();
//            }
//            Yii::$app->response->cookies->add(new Cookie([
//                'name' => 'user_authenticate',
//                    'value' => date('Y-m-d H:i:s'),
//                    'httpOnly' => false,
//                    'expire' => time() + 1800,
//                ]));
//        }
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

    public function actionIndex($invite = false)
    {
        if ($invite) {
            $cookies = Yii::$app->request->cookies;
            $id_old = $cookies->get('invite');

            if (!$id_old) {
                $cookies = Yii::$app->response->cookies;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'invite',
                    'value' => $invite,
                    'expire' => time() + 15811200,
                ]));
            }
        }

        if (Yii::$app->language == 'ru') {
            $model = Options::getOptionsAsots(Options::keys_home_page_ru);
//            \Yii::$app->view->registerMetaTag([
//                'name' => 'description',
//                'content' => 'Международный Брокер. Благодаря invest24 инвесторы могут вкладывать и следить за торговлей лучших управляющих финансового рынка, минуя каких-либо посредников.'
//            ]);
        } else {
            $model = Options::getOptionsAsots(Options::keys_home_page_en, '_EN');
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(Url::to(['/']));
        }
        $this->layout = 'registration';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if (isset($_GET['return_url'])) {
                return $this->redirect($_GET['return_url']);
            }

            return $this->redirect('/user/index');
        } else {

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        Yii::$app->user->logout();

        Yii::$app->response->cookies->remove('user_authenticate');
        Yii::$app->response->cookies->remove('show_banner');
        Yii::$app->response->cookies->remove('banner_shown');
        Yii::$app->response->cookies->remove('bets_update');

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionPi()
    {
        echo phpinfo();
        die;
    }

    /**
     * Displays static page.
     *
     * @return mixed
     */
    public function actionPage($id, $synonym)
    {
        $page = Page::findIdentity($id);
        $page = Servis::getInstance()->translete($page);
        return $this->render('page', [
            'model' => $page
        ]);
    }

    public function actionAbout()
    {
        $option = Options::getOptionsAsots(Options::keys_about);
        return $this->render('about', [
            'option' => $option,
        ]);
    }

    public function actionRegulations()
    {
        return $this->render('rules');
    }


    public function actionProfitability()
    {
        $this->view->title = Yii::t('app', 'Доходность');
        if(isset($_GET['clear'])) {
            Yii::$app->cache->delete('statistic_home');
        }
        $data = Yii::$app->cache->getOrSet('statistic_home', function () {
            return DaysLog::getTable(true);
        });
        return $this->render('profitability', [
            'data' => $data,
        ]);
    }

    public function actionContacts()
    {
        return $this->render('pages/contacts');
    }


    public function actionPartnership()
    {
        if (Yii::$app->language == 'ru') {
            $model = Options::getOptionsAsots(Options::keys_partnership_ru);
        } else {
            $model = Options::getOptionsAsots(Options::keys_partnership_en, '_en');
        }

        return $this->render('pages/partnership', [
            'model' => $model,
        ]);
    }


    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $this->layout = 'registration';
        $model = new SignupFormStep2Form();
        $sms_enabled = SmsManager::getActiveSmsProvider() ? true : false;
        $step1 = true;
        if ($model->load(Yii::$app->request->post())) {
            $step1 = false;
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->redirect(Url::to(['/user/index']));
                }
            }

        }

        return $this->render('signup', [
            'model' => $model,
            'sms_enabled' => $sms_enabled,
            'step1' => $step1
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public
    function actionRequestPasswordReset()
    {
        $this->layout = 'registration';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                $model->message = Yii::t('app', 'Проверьте свою электронную почту для получения дальнейших инструкций.');
            } else {
                $model->addError('email', Yii::t('app', 'К сожалению, мы не можем сбросить пароль для указанного адреса электронной почты.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public
    function actionResetPassword($token)
    {
        $this->layout = 'registration';

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Новый пароль сохранен.');

            return $this->redirect(Url::to(['/user/index']));
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public
    function actionVklogin()
    {
        $res = User::setVk_acaunt();
        if ($res['success']) {
            return $this->redirect(Url::to(['/user/index']));
        } else if ($res['success'] == false) {
            Yii::$app->session->setFlash('error', $res['message']);
            $this->redirect(Url::to(['/site/login']));
        }
        return $this->render('vklogin', []);
    }

    public
    function actionAjax()
    {
        if (Yii::$app->request->isAjax) {
            //уже не используется убрать, но не в этом коммите
            if (isset($_POST['action']) && $_POST['action'] == 'sendSMS') {

                $phone = User::clearPhone($_POST['data']);
                if (!is_numeric($phone)) {
                    echo json_encode(['success' => false, 'message' => "Неверно указан номер. Укажите Международном формате"]);
                    exit;
                }
                $code = rand(10000, 99999);
                $confirm = new LogConfirm();
                $confirm->date_add = time();
                $confirm->phone = $phone;
                $confirm->code = $code;
                $confirm->save();

                if ($mess = SmsManager::stopSpam($phone, 'Регистрация')) {
                    return json_encode(['success' => false, 'message' => $mess]);
                }

                $number = User::clearPhone($phone);
                $current_sms_count = SmsLog::find()->where(['phone' => $number])->andWhere('date_add > "'.date('Y-m-d H:i:s', strtotime(' -1 minutes')). '"')->count() >= 3 ? true : false;
                if($current_sms_count) {
                    $cookies = Yii::$app->response->cookies;
                    $cookies->add(new \yii\web\Cookie([
                        'name' => 'registration_sms_block',
                        'value' => 1,
                        'expire' => strtotime('+5 minutes'),
                    ]));
                    return json_encode(['success' => false, 'message' => 'Вы слишком часто запрашивали код. Попробуйте через 5 минут.', 'block_sms_button' => true]);
                } 

                if (SmsManager::sendOne(SmsTemplate::templateRegistrationConfirm, $phone, ['code' => $code])) {
                    echo json_encode(['success' => true, 'message' => "Код отправлен"]);
                } else {
                    echo json_encode(['success' => false, 'message' => "Возникла непредвиденная ошибка. Обратитесь в поддержку"]);
                }

                exit;
            } else if (isset($_POST['action']) && $_POST['action'] == 'fb_login') {

                echo json_encode(User::setFB_acaunt());
                exit;
            } else if (isset($_POST['action']) && $_POST['action'] == 'vk_login') {

                echo json_encode(User::setVK_acaunt());
                exit;
            }
        }
    }

    public
    function actionEmailconfirm($code)
    {
        $this->layout = 'registration';
        if (!$confirm = LogConfirm::find()->orderBy('date_add DESC')->where('code = "' . $code . '" AND date_add > ' . (time() - 3600))->one()) {
            return $this->goHome();
        }

        if (!$user = User::findByEmail($confirm->email)) {
            return $this->goHome();
        }

        $user->email_confirm = true;
        $user->save();

        return $this->render('emailconfirm');
    }

    public
    function actionLoadMoreManagerReviews()
    {
        if (Yii::$app->request->isAjax) {
            $account_id = Yii::$app->request->post('account');
            $offset = Yii::$app->request->post('items');
            $data = false;
            if (!$account = TradingAccount::findIdentity($account_id) or !$offset) {
                return json_encode($data);
            }

            $data = $account->getReviews(6, $offset);
            return json_encode($data);
        }
    }

    public
    function actionLoadMoreSolutionReviews()
    {
        if (Yii::$app->request->isAjax) {
            $solution_id = Yii::$app->request->post('solution');
            $offset = Yii::$app->request->post('items');
            $data = false;
            if (!$solution = Solution::findIdentity($solution_id) or !$offset) {
                return json_encode($data);
            }

            $data = $solution->getReviews(6, $offset);
            return json_encode($data);
        }
    }

    public
    function actionLoadMoreMixedReviews()
    {
        if (Yii::$app->request->isAjax) {
            $offset = Yii::$app->request->post('items');
            $data = Solution::getMixedReviews(6, $offset);
            return json_encode($data);
        }
    }

    public
    function actionLoadManagerReviews()
    {
        if (Yii::$app->request->isAjax) {
            $account_id = Yii::$app->request->post('account');
            $data = false;
            if (!$account = TradingAccount::findIdentity($account_id)) {
                return json_encode($data);
            }

            $data['reviews'] = $account->getReviews();
            $data['account_rating'] = number_format($account->getReviewsRating(), 1, '.', '');
            $data['reviews_count'] = $account->countReviews();
            $data['manager_comment'] = nl2br($account->comment);
            return json_encode($data);
        }
    }

    public
    function actionLoadMixedReviews()
    {
        if (Yii::$app->request->isAjax) {

            $data['reviews'] = Solution::getMixedReviews();
            $data['account_rating'] = 0;
            $data['reviews_count'] = Solution::countMixedReviews();
            $data['manager_comment'] = null;
            return json_encode($data);
        }
    }

    public
    function actionAddManagerAnswer()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (Yii::$app->request->isAjax) {
            $review_id = Yii::$app->request->post('review_id');
            $answer = Yii::$app->request->post('answer');
            $data = false;
            if (!$review = ManagerReviews::findIdentity($review_id) OR strlen($answer) < 10 OR !$account = TradingAccount::findIdentity($review->trading_account_id) OR $account->user_id != Yii::$app->user->id) {
                return json_encode($data);
            }

            $review->answer = $answer;
            if ($review->save()) {
                return json_encode(['manager' => $account->name]);
            }
            return false;
        }
    }

    public
    function actionAddVisit()
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request->post('data');
            $values = array();
            parse_str($data, $values);

            if (!VisitorLog::find()->where(['phone' => $values['visit_phone'], 'name' => $values['visit_name'], 'city_id' => $values['visit_city'], 'date_visit' => $values['visit_date'], 'sms_confirmed' => 0])->andWhere('date_add > "' . date('Y-m-d H:i:s', strtotime(' -10 minutes')) . '"')->exists()) {
                if (!VisitorLog::makeRecord($values['visit_name'], $values['visit_phone'], $values['visit_date'], $values['visit_city'])) {
                    return json_encode(['status' => 2, 'message' => 'Некорректные данные']);
                }
            }


            if (SmsManager::getActiveSmsProvider()) {
                if ($block = SmsManager::stopSpam($values['visit_phone'], 'Запись на визит')) {
                    return json_encode(['status' => 2, 'message' => $block]);
                }

                $code = rand(10000, 99999);
                $confirm = new LogConfirm();
                $confirm->date_add = time();
                $confirm->phone = User::clearPhone($values['visit_phone']);
                $confirm->code = $code;
                $confirm->save();

                $res = SmsManager::sendOne(9, $values['visit_phone'], ['code' => $code]);
                if (!$res) {
                    return json_encode(['status' => 2, 'message' => 'Возможно вы ввели неверный телефон. Проверьте данные']);
                }
            } else {
                return json_encode(['status' => 3, 'message' => 'Возникли технические неполадки. Свяжитесь с администрацией']);
            }

            return json_encode(['status' => 1, 'message' => '']);
        }
    }

    public
    function actionConfirmVisit()
    {
        if (Yii::$app->request->isAjax) {
            $phone = Yii::$app->request->post('phone');
            $code = Yii::$app->request->post('code');
            if (!LogConfirm::find()->where(['phone' => User::clearPhone($phone), 'code' => $code])->andWhere('date_add >= ' . (time() - 60 * 10))->exists() OR !$log = VisitorLog::find()->where(['phone' => User::clearPhone($phone)])->one()) {
                return json_encode(['status' => 2]);
            }
            $log->sms_confirmed = 1;
            $log->save();
            return json_encode(['status' => 1]);
        }
    }

    public
    function actionTest()
    {
        if ($_SERVER["REMOTE_ADDR"] !== '127.0.0.1') {
            return $this->goHome();
        }
        $api = Bankcomat::getInstance();
//        $result = $api->query("order-get", [
//            "skey" => null, // Опционально
//            "order_id" => "15420985044907"
//        ]);

//        $result = $api->query("order-get", [
//            "skey" => null, // Опционально
//            "order_id" => "15420985044907"
//        ]);
//        $result = $api->query("order-create", [
//            "vkey" => md5(Yii::$app->user->id),
//            "Order" => [
//                "psid1" => 26,
//                "psid2" => 57,
//                "in" => 5000,
//                "out" => 5000,
//                "direct" => 1,
//                "agreement" => "yes",
//                "props" => [
//                    ["name" => "email","value" => "namtyuetryurtyue@mail.ru"],
//                    ["name" => "from_acc", "value" => "6234567890123456"],
//                    ["name" => "from_fio", "value" => "Иванов Иван Иванович"],
//                    ["name" => "to_acc", "value" => "R891984011318"]
//                ]
//            ]
//        ]);
//        $order_id = $result->id;
//        $result = $api->query("order-validate", [
//            "skey" => null, // Опционально
//            "order_id" => $order_id
//        ]);
//        $result = $api->query("order-pay-info", [
//            "skey" => null, // Опционально
//            "order_id" => $order_id
//        ]);


        $result = $api->query("payment-systems", [
            //'akey' => 'dcd660f6190228f595beae51abd238f48bec408c671bf80db8fb4b3c4fb8e9c0',
            //"psid" => 15
        ]);
        echo '<pre>';
        echo 'Status: ' . $api->status . " " . $api->statustext . PHP_EOL;
        echo 'Error: ' . $api->error . PHP_EOL;
        echo 'Value:' . PHP_EOL;
        print_r($result);
        echo "===========\n";
        print_r($api);
        echo '</pre>';

        die;
    }

    public
    function actionGetSaveCapitalConsult()
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }
        if (!AmoQueue::find()->where(['task' => 'actionAddLeadSaveCapital', 'params' => Yii::$app->user->id, 'additional_params' => serialize(2)])->exists()) {
            AmoQueue::addTask('actionAddLeadSaveCapital', Yii::$app->user->id, serialize(2));
        }
        return true;
    }


    public function actionSignup_step()
    {
        if (Yii::$app->request->isAjax) {
            $phone = User::clearPhone(Yii::$app->request->post('phone'));
            $sms_code = trim(Yii::$app->request->post('sms_code'));
            $username = trim(Yii::$app->request->post('username'));
            if (!$username) {
                $data['errors']['username'] = Yii::t('app', 'Необходимо заполнить');
            }
            if (!$phone) {
                $data['errors']['phone'] = Yii::t('app', 'Необходимо заполнить');
            }
            $data['success'] = false;
            if (isset($data['errors'])) {
                return json_encode($data);
            }

            if (User::find()->where("username = '$username'")->exists()) {
                $data['errors']['username'] = Yii::t('app', 'Данный логин уже занят');
            }
            if (User::find()->where("phone = '$phone'")->exists()) {
                $data['errors']['phone'] = Yii::t('app', 'Номер телефона уже зарегистрирован в системе');
            }

            if (SmsManager::getActiveSmsProvider()) {
                if (!$sms_code OR !$confirm = LogConfirm::find()->orderBy(' date_add DESC')->where('phone = ' . $phone . ' AND date_add > ' . (time() - 600))->one() OR $confirm->code != $sms_code) {
                    $data['errors']['sms_code'] = Yii::t('app', 'СМС КОД НЕВЕРНЫЙ');
                }

            }
            if (!isset($data['errors'])) {
                $data['success'] = true;
            }

            return json_encode($data);
        }
    }

    public function actionGetData()
    {
        if (Yii::$app->request->isAjax) {
            $data['new_news'] = NewsUserRed::getNewNews();
            return json_encode($data);

            $cookies = Yii::$app->request->cookies;
            $h = date('H');
            $count = 0;
            if ($cookies->has('bets_update')) {
                $count = $cookies->getValue('bets_update');
            }
            if ($h < 10) {
                $date_from = date('Y-m-d 15:00:00', strtotime(' yesterday'));
            } else {
                $date_from = date('Y-m-d 15:00:00');
            }
            $date_to = date('Y-m-d 10:00:00', strtotime($date_from . ' +1 day'));
            $events_count = Events::find()->where('`show` = 1 AND date_add BETWEEN "' . $date_from . '" AND "'.$date_to.'"')->count();

        }
    }


    public function actionGetMoreChatMessages()
    {
        if (Yii::$app->request->isAjax) {
           $e = Yii::$app->request->post('e');
            $data['messages'] = ChatMessage::find()
                ->where(['deleted_at' => null, 'parent_id' => null])
                ->with(['user', 'childs'])
                ->orderBy('id DESC, date_add ASC')
                ->limit(10)
                ->offset($e)
                ->all();
            $data['has_more'] = ChatMessage::find()
                ->where(['deleted_at' => null, 'parent_id' => null])
                ->count() > $e + 10 ? true : false;
            $messages = '';
            foreach ($data['messages'] as $m) {
                $messages .= ChatWidget::addMessage($m, Yii::$app->user->id, Yii::$app->user->isGuest ? false : Yii::$app->user->identity->can('moderator'));
            }
            $data['messages'] = $messages;
            return json_encode($data);
        }
    }

    public function actionChatDelete()
    {
        if (Yii::$app->request->isAjax) {
            if(Yii::$app->user->isGuest OR !Yii::$app->user->identity->can('moderator')) {
                return false;
            }
            $mi = Yii::$app->request->post('mi');
            ChatMessage::deleteMessage($mi, Yii::$app->user->id);
        }
    }

    public function actionDevelopment()
    {
      //  return file_get_contents('./../web/upload/development.zip');
    }
}
