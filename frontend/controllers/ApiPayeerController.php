<?php

namespace frontend\controllers;

use common\models\Overdraft;
use common\models\PaymentLog;
use common\models\User;
use common\service\api_terminal\Cryptonator;
use common\service\api_terminal\Fkassa;
use common\service\api_terminal\Freekassa;
use common\service\api_terminal\FreeObmen;
use common\service\api_terminal\PayinPayout;
use common\service\api_terminal\Perfectmoney;
use common\service\api_terminal\Piastrix;
use common\service\PartnerProgram;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\service\LogMy;
use common\service\api_terminal\Payeer;
use common\models\BalanceLog;
use common\models\BonusDebt;
use common\service\api_terminal\Megatransfer;
use common\service\api_terminal\Ultrapays;

/**
 * Site controller
 */
class ApiPayeerController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        Yii::$app->request->enableCsrfValidation = false;
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['get'],
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {

        var_dump('index');
    }

    /*---------------------------------------------------------*/
    // Payeer money
    public function actionSuccess()
    {
        usleep(10000 * rand(50, 100));
        $post = (!empty($_POST)) ? $_POST : $_GET;
        LogMy::getInstance()->setLog(['method' => 'success', 'post' => $_POST, 'get' => $_GET], 'payerr');

        if (isset($post['m_operation_id']) && isset($post['m_sign'])) {
            $arHash = array(
                $post['m_operation_id'],
                $post['m_operation_ps'],
                $post['m_operation_date'],
                $post['m_operation_pay_date'],
                $post['m_shop'],
                $post['m_orderid'],
                $post['m_amount'],
                $post['m_curr'],
                $post['m_desc'],
                $post['m_status']
            );
            if (isset($post['m_params'])) {
                $arHash[] = $post['m_params'];
            }

            $sign_hash = Payeer::getInstance()->getHesh($arHash);

            if ($post['m_sign'] == $sign_hash && $post['m_status'] == 'success' && $post['m_curr'] == 'USD') {
                //сам перевод
                if (!$order = PaymentLog::findIdentity($post['m_orderid']) OR $order->payment_system != BalanceLog::payeer OR $order->completed == true) {
                    return false;
                }

                $user = User::findIdentity($order->user_id);
                if (!$user) {
                    LogMy::getInstance()->setLog(['user not found' => $user], 'payerr');
                    $this->showResultPayEer($post['m_orderid'], false);
                }

                $summ_user = Overdraft::closeDolg($user->id, (int)$post['m_amount']);


                $tmp_before_balance = $user->balance;
//                $user->balance += $summ_user;
//                $res = $user->save();

                LogMy::getInstance()->setLog(['user->balance before' => $tmp_before_balance, 'user->balance after' => $user->balance, 'summ add' => $summ_user], 'payerr');


                $order->completed = true;
                $order->save();

                $user->giveFirstBonus();
                $comment = 'Пополнение через PayEer';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, (int)$post['m_amount'], BalanceLog::deposit, BalanceLog::in_process, BalanceLog::payeer, null, $comment, null, $post['m_sign'], null, null, false, $order->payway_id);
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                } else {
                    LogMy::getInstance()->setLog(['not add in balance log' => ''], 'payerr');
                }
                LogMy::getInstance()->setLog(['user summ' => $summ_user, 'user id' => $user->id], 'payerr');
                $this->showResultPayEer($post['m_orderid'], true);


                LogMy::getInstance()->setLog(['not add balance user. res' => 1, 'user id' => $user->id], 'payerr');
                $this->showResultPayEer($post['m_orderid'], false);

            }
            LogMy::getInstance()->setLog(['bad sign_hash or m_status or m_curr $sign_hash = ' => $sign_hash], 'payerr');
            $this->showResultPayEer($post['m_orderid'], false);
        } else {
            LogMy::getInstance()->setLog(['mess' => 'no enter in if'], 'payerr');
            $this->showResultPayEer($post['m_orderid'], false);
        }
    }

    public function actionStatus()
    {
        LogMy::getInstance()->setLog(['method' => 'status', 'post' => $_POST, 'get' => $_GET], 'payerr');
        var_dump('status');
    }

    private function showResultPayEer($m_orderid, $success = true)
    {
        if ($success) {
            echo $m_orderid . '|success';
        } else {
            echo $m_orderid . '|error';
        }
        LogMy::getInstance()->setLog(['response' => $success], 'payerr');
        echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        exit;
    }


    /*-----------------------------------------------------------*/
    //advcash
    public function actionAdvcashSuccess()
    {
        LogMy::getInstance()->setLog(['method' => 'actionAdvcashSuccess', 'post' => $_POST, 'get' => $_GET], 'advcash');

    }

    public function actionAdvcashFail()
    {
        LogMy::getInstance()->setLog(['method' => 'actionAdvcashFail', 'post' => $_POST, 'get' => $_GET], 'advcash');

    }

    public function actionAdvcashStatus()
    {
        LogMy::getInstance()->setLog(['method' => 'actionAdvcashStatus', 'post' => $_POST, 'get' => $_GET], 'advcash');

    }

    /*----------------------------------------------------------*/
    // PerfectMoney
    public function actionPerfectPayment()
    {
        usleep(10000 * rand(50, 100));

        //        $_POST = [
//            "PAYEE_ACCOUNT"=> "U16228238",
//            "PAYMENT_ID"=> "22_1521720581",
//            "PAYMENT_AMOUNT"=> "1",
//            "PAYMENT_UNITS"=> "USD",
//            "PAYMENT_BATCH_NUM"=> "209136608",
//            "PAYER_ACCOUNT"=> "U16228238",
//            "TIMESTAMPGMT"=> "1521716866",
//            "V2_HASH"=> "01F338E5E0B85F73123858E0B0111B6D",
//            "BAGGAGE_FIELDS"=> "",
//        ];

        LogMy::getInstance()->setLog(['method' => 'actionPerfectPayment', 'post' => $_POST, 'get' => $_GET], 'perfectmoney');

        $string =
            $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
            $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
            $_POST['PAYMENT_BATCH_NUM'] . ':' .
            $_POST['PAYER_ACCOUNT'] . ':' . strtoupper(md5(Perfectmoney::getInstance()->alternate_passhprase)) . ':' .
            $_POST['TIMESTAMPGMT'];

        $hash = strtoupper(md5($string));

        $log = ['success' => false, 'message' => 'bad hash'];
        if ($hash == $_POST['V2_HASH']) { // processing payment if only hash is valid
            //сам перевод
            $tmp = explode('_', $_POST['PAYMENT_ID']);
            $user = User::findIdentity($tmp[0]);
            if (!$user OR BalanceLog::find()->where(['user_id' => $user->id, 'hash_payment' => $_POST['PAYMENT_BATCH_NUM']])->exists()) {
                $log['message'] = 'Нет такого пользователя или платеж был оплачен';
            } else {

                $summ_user = Overdraft::closeDolg($user->id, (int)$_POST['PAYMENT_AMOUNT']);

//                $user->balance += $summ_user;
                $res = $user->save();
            }

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через PerfectMoney';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, (int)$_POST['PAYMENT_AMOUNT'], BalanceLog::deposit, BalanceLog::in_process, BalanceLog::perfectmoney, null, $comment, null, $_POST['PAYMENT_BATCH_NUM']);
                $log['message'] = 'Деньги пользователю зачислены';
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                    $log['success'] = true;
                }
            }

        }
        LogMy::getInstance()->setLog(['method' => 'response', 'mes' => $log], 'perfectmoney');
        echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        exit;
    }


    public function actionInterkassaPayment()
    {
        usleep(10000 * rand(50, 100));

        LogMy::getInstance()->setLog(['method' => 'actionInterkassaPayment', 'post' => $_POST, 'get' => $_GET], 'interkassa');

        $post = $_POST;
        if (!$order = PaymentLog::findIdentity($post['ik_pm_no']) OR $order->payment_system != BalanceLog::interkassa OR $order->completed == true) {
            return $this->redirect(['/user/history']);
        }

        if ($post['ik_inv_st'] == "success") {

            $user = User::findIdentity($order->user_id);

            $summ_to = $user->balance;
            if (!$user) {
                $log['message'] = 'Нет такого пользователя';
            } else {
                $user->balance += $order->size;
                $summ_after = $user->balance;
                $res = $user->save();
            }

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через Interkassa';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::interkassa, null, $comment, null, null, false, $order->payway_id);
                $log['message'] = 'Деньги пользователю зачислены. Было = ' . $summ_to . ' стало = ' . $summ_after . ' добавили = ' . $order->size;
                if ($balanse_log->save()) {
                    $log['success'] = true;
                }
            }
            $order->system_payment_id = $post['ik_inv_id'];
            $order->completed = true;
            $order->save();

            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        }

        LogMy::getInstance()->setLog(['method' => 'response actionInterkassaPayment', 'mes' => $log], 'interkassa');
    }

    public function actionMegatransferPayment()
    {
        usleep(10000 * rand(50, 100));
//  {"items":"Clothes",
//"quantity":"2",
//"amount":"2.00",
//"currency":"USD",
//"total_amount":"4.00",
//"merchant_id":"526600186654",
//"order_id":"1439280592",
//"status":"failed",
//"payment_method":"CC"
//"message":"Internal error on the system. Please contact support."}
        parse_str(file_get_contents("php://input"), $_POST);
        LogMy::getInstance()->setLog(['method' => 'actionMegatransferPayment', 'post' => $_POST, 'get' => $_GET], 'Megatransfer');

        $response = $_POST["response"];
        $post = json_decode($response, true);
        LogMy::getInstance()->setLog(['decoded_post' => $post], 'Megatransfer');

        $order_id = str_replace(Megatransfer::getInstance()->merchant_id . "_", null, $post['order_id']);
        if (!$order = PaymentLog::findIdentity($order_id) OR $order->payment_system != BalanceLog::megatransfer OR $order->completed == true) {
            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        }

        $transaction_id = $post["transaction_id"];
        $secret_code = Megatransfer::getInstance()->secret;
        $token = $post['token'];
        $hash = hash("md5", $secret_code . $post['order_id'] . $transaction_id . $secret_code);
        LogMy::getInstance()->setLog(['hash' => $hash], 'Megatransfer');
        if ($post['status'] == "successful" AND $token == $hash) {

            $user = User::findIdentity($order->user_id);

            if (!$user) {
                $log['message'] = 'Нет такого пользователя';
            }


            $summ_user = Overdraft::closeDolg($user->id, $order->size);
            $user->balance += $summ_user;
            $res = $user->save();

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через Interkassa';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::megatransfer, null, $comment, null, null, false, $order->payway_id);
                $log['message'] = 'Деньги пользователю зачислены';
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                    $log['success'] = true;
                }
            }
            $order->system_payment_id = $post['transaction_id'];
            $order->completed = true;
            $order->save();

            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        }

        LogMy::getInstance()->setLog(['method' => 'response', 'mes' => $log], 'Megatransfer');
    }

    public function actionInterkassaError()
    {
        LogMy::getInstance()->setLog(['method' => 'actionInterkassaError', 'post' => $_POST, 'get' => $_GET], 'interkassa');
        echo 'Ошибка при внесении оплаты с помощью Interkassa, пожалуйста обратитесь к администратору';
    }

    public function actionMegatransferError()
    {
        LogMy::getInstance()->setLog(['method' => 'actionMegatransferPayment', 'post' => $_POST, 'get' => $_GET], 'Megatransfer');
        echo 'Ошибка при внесении оплаты с помощью Megatransfer, пожалуйста обратитесь к администратору';
    }

    public function actionPerfectStatus()
    {
        usleep(10000 * rand(50, 100));
        LogMy::getInstance()->setLog(['method' => 'actionPerfectStatus', 'post' => $_POST, 'get' => $_GET], 'perfectmoney');
        $this->actionPerfectPayment();

    }


    /*----------------------------------------------------------*/
    // Cryptonator
    public function actionCryptonator()
    {
        usleep(10000 * rand(50, 100));
//        $_POST = [
//            "merchant_id" => "f977e340ffa0596f044ec067d498f458",
//            "invoice_id" => "56368c1bcb4ee5062cf42f6fdd4a4fd4",
//            "invoice_created" => "1521725034",
//            "invoice_expires" => "1521726834",
//            "invoice_amount" => "1.00000000",
//            "invoice_currency" => "usd",
//            "invoice_status" => "paid",
//            "invoice_url" => "https://ru.cryptonator.com/merchant/invoice/56368c1bcb4ee5062cf42f6fdd4a4fd4",
//            "order_id" => "22_1521725051",
//            "checkout_address" => "17zRpdXTcbSD5yA4RpJva6w1So4wegB8Lg",
//            "checkout_amount" => "0.00010000",
//            "checkout_currency" => "bitcoin",
//            "date_time" => "1521725051",
//            "secret_hash" => "914ed78e1b9309492e7d22d8af7349d0cd4b03cf",
//        ];

        LogMy::getInstance()->setLog(['method' => 'actionCryptonator', 'post' => $_POST, 'get' => $_GET], 'cryptonator');

        $exist = BalanceLog::find()->where(['hash_payment' => $_POST['invoice_id']])->all();
        if ($exist) {
            LogMy::getInstance()->setLog(['method' => 'response', 'mess' => 'Платеж уже существует'], 'cryptonator');
            return;
        }
        if ($_POST['invoice_currency'] != 'usd') {
            LogMy::getInstance()->setLog(['method' => 'response', 'mess' => 'Не та валюта'], 'cryptonator');
            return;
        }

        $array = $_POST;
        unset($array['secret_hash']);
        $string = implode('&', $array) . '&' . Cryptonator::getInstance()->secret;
        $string = sha1($string);

        if ($_POST['secret_hash'] == $string) {

            //все норм сохраняем
            $log = ['success' => false, 'message' => 'unpaid'];
            if (isset($_POST['invoice_status'])) {
                $log['message'] = $_POST['invoice_status'];
            }
            if ($_POST['invoice_status'] == 'paid'
                || $_POST['invoice_status'] == 'mispaid'
                || $_POST['invoice_status'] == 'confirming'
            ) {

                $tmp = explode('_', $_POST['order_id']);
                $user = User::findIdentity($tmp[0]);
                if (!$user) {
                    $log['message'] = 'Нет такого пользователя';
                } else {
                    $user->balance += (int)$_POST['invoice_amount'];
                    $res = $user->save();
                }

                if ($res) {
                    $user->giveFirstBonus();
                    $comment = 'Пополнение через Cryptonator';
                    $balanse_log = new BalanceLog();
                    $balanse_log->addLog($user->id, (int)$_POST['invoice_amount'], BalanceLog::deposit, BalanceLog::in_process, BalanceLog::cryptonator, null, $comment, null, $_POST['invoice_id']);
                    $log['message'] = 'Деньги пользователю зачислены';
                    if ($balanse_log->save()) {
                        BonusDebt::payOutUserDebts($user->id);
                        $log['success'] = true;
                    }

                }
            }
        } else {
            $log = ['success' => false, 'message' => 'bad hash'];
        }

        LogMy::getInstance()->setLog(['method' => 'response', 'mes' => $log], 'cryptonator');
        if ($log['success']) {
            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        } else {
            echo json_encode($log);
        }

    }


    public function actionBankomat()
    {
        usleep(10000 * rand(50, 100));

        LogMy::getInstance()->setLog(['method' => 'actionBankomat', 'post' => $_POST, 'get' => $_GET], 'bankcomat');

        $post = $_POST;
//        if($post['value']['status'] !== 'completed') {
//            return false;
//        }

        if (!$order = PaymentLog::find()->where(['system_payment_id' => $post['value']['order_id']])->one() OR $order->payment_system != BalanceLog::bankcomat OR $order->completed == true) {
            return false;
        }

        if ($post['value']['status'] == 'completed') {

            $user = User::findIdentity($order->user_id);

            $summ_to = $user->balance;
            if (!$user) {
                $log['message'] = 'Нет такого пользователя';
            } else {
                $summ_user = Overdraft::closeDolg($user->id, $order->size);
                $user->balance += $summ_user;
                $summ_after = $user->balance;
                $res = $user->save();
            }

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через bankcomat';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::bankcomat, null, $comment, null, null, false, $order->payway_id);
                $log['message'] = 'Деньги пользователю зачислены. Было = ' . $summ_to . ' стало = ' . $summ_after . ' добавили = ' . $summ_user;
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                    $log['success'] = true;
                }
            }
            $order->system_payment_id = $post['ik_inv_id'];
            $order->completed = true;
            $order->save();

            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
        }

        LogMy::getInstance()->setLog(['method' => 'response actionBankomat', 'mes' => $log], 'bankcomat');
    }

    public function actionUltrapaysPayment()
    {
        usleep(10000 * rand(50, 100));

        LogMy::getInstance()->setLog(['method' => 'actionUltrapaysPayment', 'post' => $_POST, 'get' => $_GET], 'ultrapays');

        $post = $_POST;
        if (empty($post)) {
            $post = $_GET;
        }
        if (empty($post)) {
            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
            return false;
        }

        if (!$order = PaymentLog::findIdentity($post['uid']) OR $order->payment_system != BalanceLog::ultrapays OR $order->completed == true) {
            return false;
        }
        $ultrapays = Ultrapays::getInstance();
        $log = null;
        $hash = md5($post['id'] . $post['uid'] . $ultrapays->merchant_id . $ultrapays->secret);
        if ($hash == $post['hash']) {

            $user = User::findIdentity($order->user_id);

            $summ_to = $user->balance;
            if (!$user) {
                $log['message'] = 'Нет такого пользователя';
            } else {
                $summ_user = Overdraft::closeDolg($user->id, $order->size);
                $user->balance += $summ_user;
                $summ_after = $user->balance;
                $res = $user->save();
            }

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через Ultrapays';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::ultrapays, null, $comment, null, null, false, $order->payway_id);
                $log['message'] = 'Деньги пользователю зачислены. Было = ' . $summ_to . ' стало = ' . $summ_after . ' добавили = ' . $summ_user;
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                    $log['success'] = true;
                }
            }
            $order->system_payment_id = $post['id'];
            $order->completed = true;
            $order->save();


        }

        LogMy::getInstance()->setLog(['method' => 'response actionUltrapaysPayment', 'mes' => $log], 'ultrapays');
        echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
    }

    public function actionUltrapaysBack()
    {
        usleep(10000 * rand(50, 100));

        LogMy::getInstance()->setLog(['method' => 'actionUltrapaysBack', 'post' => $_POST, 'get' => $_GET], 'ultrapays');

        $post = $_POST;
        if (empty($post)) {
            $post = $_GET;
        }
        if (empty($post)) {
            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
            return false;
        }

        if (!$order = PaymentLog::findIdentity($post['uid']) OR $order->payment_system != BalanceLog::ultrapays OR $order->completed == true) {
            return false;
        }
        $log = null;
        $ultrapays = Ultrapays::getInstance();
        $hash = md5($post['uid'] . $ultrapays->merchant_id . $ultrapays->secret);
        if ($hash == $post['hash'] AND $post['status'] == 'success' AND $post['message'] == 'complete') {

            $user = User::findIdentity($order->user_id);

            $summ_to = $user->balance;
            if (!$user) {
                $log['message'] = 'Нет такого пользователя';
            } else {
                $summ_user = Overdraft::closeDolg($user->id, $order->size);
                $user->balance += $summ_user;
                $summ_after = $user->balance;
                $res = $user->save();
            }

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через Ultrapays';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::ultrapays, null, $comment, null, null, false, $order->payway_id);
                $log['message'] = 'Деньги пользователю зачислены. Было = ' . $summ_to . ' стало = ' . $summ_after . ' добавили = ' . $summ_user;
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                    $log['success'] = true;
                }
            }
            // $order->system_payment_id = $post['id'];
            $order->completed = true;
            $order->save();


        }

        LogMy::getInstance()->setLog(['method' => 'response actionUltrapaysBack', 'mes' => $log], 'ultrapays');
        echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
    }

    public function actionPiastrixNotice()
    {
        usleep(10000 * rand(50, 100));

        LogMy::getInstance()->setLog(['method' => 'actionPiastrixNotice', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'piastrix');

        $post = $_POST;
        if (empty($post)) {
            $post = $_GET;
        }
        if (empty($post)) {
            echo '<script> window.location.href = "' . Url::to(['/user/history'], true) . '";</script>';
            return false;
        }

        if (!in_array(self::getIP(), array('87.98.145.206', '51.68.53.104', '51.68.53.105', '51.68.53.106', '51.68.53.107', '91.121.216.63', '37.48.108.180', '37.48.108.181'))) {
            return redirect('/transactions');
        }

        if (!$order = PaymentLog::findIdentity($post['shop_order_id']) OR $order->payment_system != BalanceLog::piastrix OR $order->completed == true) {
            return false;
        }
        $log = null;
        $piastrix = Piastrix::getInstance();
        ksort($post);
        $check_array = [];
        foreach ($post as $key => $value) {
            if ($value != NULL AND $value != '' AND $key != 'sign') {
                $check_array[$key] = $value;
            }
        }

        $hash = hash('sha256', implode(':', $check_array) . $piastrix->secret);
        $log['hash'] = 'check hash = ' . $hash;

        if ($post['status'] == 'success') {

            $user = User::findIdentity($order->user_id);

            $summ_to = $user->balance;
            if (!$user) {
                $log['message'] = 'Нет такого пользователя';
            } else {
                $summ_user = Overdraft::closeDolg($user->id, $order->size);
                $user->balance += $summ_user;
                $summ_after = $user->balance;
                $res = $user->save();
            }

            if ($res) {
                $user->giveFirstBonus();
                $comment = 'Пополнение через Piastrix';
                $balanse_log = new BalanceLog();
                $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::piastrix, null, $comment, null, null, false, $order->payway_id);
                $log['message'] = 'Деньги пользователю зачислены. Было = ' . $summ_to . ' стало = ' . $summ_after . ' добавили = ' . $summ_user;
                if ($balanse_log->save()) {
                    BonusDebt::payOutUserDebts($user->id);
                    $log['success'] = true;
                }
            }
            $order->system_payment_id = $post['payment_id'];
            $order->completed = true;
            $order->save();


        }

        LogMy::getInstance()->setLog(['method' => 'response actionPiastrixNotice', 'mes' => $log], 'piastrix');
        echo 'Ok';
    }

    public static function getIP()
    {
        if (isset($_SERVER['HTTP_X_REAL_IP'])) return $_SERVER['HTTP_X_REAL_IP'];
        return $_SERVER['REMOTE_ADDR'];
    }

    public function actionPiastrixSuccess()
    {
        LogMy::getInstance()->setLog(['method' => 'actionPiastrixSuccess', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'piastrix');
        $this->redirect(['/user/history']);
    }

    public function actionPiastrixFail()
    {
        LogMy::getInstance()->setLog(['method' => 'actionPiastrixFail', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'piastrix');
        $this->redirect(['/user/index']);
    }

    public function actionFreekassaSuccess()
    {
        LogMy::getInstance()->setLog(['method' => 'actionFreekassaSuccess', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'freekassa');
        $post = $_POST;
        if (empty($post)) {
            $post = $_GET;
        }
        $request = $post;

        $freekassa_merchant = Freekassa::getInstance();
        $secret_str = $freekassa_merchant->secret2;
        $merchant_id = $freekassa_merchant->merchant_id;


        if (!($merchant_id && $secret_str)) {
            return $this->redirect(['/user/history']);
        }

        $sign = md5(
            $merchant_id . ':' . $request['AMOUNT'] . ':' . $secret_str . ':' . $request['MERCHANT_ORDER_ID']
        );


        if ($sign != $request['SIGN']) {
            return $this->redirect(['/user/history']);
            //return redirect('/transactions');
        }

        $unique_code = $request['MERCHANT_ORDER_ID'];

        if (!$order = PaymentLog::findIdentity($unique_code) OR $order->payment_system != BalanceLog::freekassa OR $order->completed == true) {
            return $this->redirect(['/user/history']);
        }


        $user = User::findIdentity($order->user_id);

        $summ_to = $user->balance;
        if (!$user) {
            $log['message'] = 'Нет такого пользователя';
        }

        $comment = 'Пополнение через Freekassa';
        $balanse_log = new BalanceLog();
        $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::freekassa, null, $comment, null, null, false, $order->payway_id);
        $balanse_log->save();

        $order->system_payment_id = $post['payment_id'];
        $order->completed = true;
        $order->save();

        return $this->redirect(['/user/history']);
//        return redirect('/transactions');
    }

    public function actionFreekassaFail()
    {
        LogMy::getInstance()->setLog(['method' => 'actionFreekassaFail', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'freekassa');
        $this->actionFreekassaSuccess();
        //$this->redirect(['/user/history']);
    }

    public function actionFreekassaNotice()
    {
        LogMy::getInstance()->setLog(['method' => 'actionFreekassaNotice', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'freekassa');
        //$this->redirect(['/user/history']);
        $this->actionFreekassaSuccess();
    }

    public function actionFkassa()
    {
        LogMy::getInstance()->setLog(['method' => 'actionFkassa', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'actionFkassa');
        $post = $_POST;
        if (empty($post)) {
            $post = $_GET;
        }
        $request = $post;
        if(!isset($request['m_operation_id']) OR !isset($request['m_sign']))
        {
            echo 200;
            $this->redirect(['/user/history']);
        }

        $merchant = Fkassa::getInstance();

        $unique_code = $request['m_order'];

        if (!$order = PaymentLog::findIdentity($unique_code) OR $order->payment_system != BalanceLog::fkassa OR $order->completed == true) {
            echo 200;
            $this->redirect(['/user/history']);
        }


        $user = User::findIdentity($order->user_id);

        if (!$user) {
            $log['message'] = 'Нет такого пользователя';
            echo 200;
            $this->redirect(['/user/history']);
        }

        $Secret  = $merchant->secret;
        $HashCod = array($request['m_shop'],
            $request['m_order'],
            $request['m_amount'],
            $request['m_unit'],
            $request['m_operation_id'],
            $request['m_operation_date'],
            $Secret);

        $sign_hash = strtoupper(hash('sha256', implode(':', $HashCod)));

        if($_POST['m_sign'] == $sign_hash)
        {
            $comment = 'Пополнение через Fkassa';
            $balanse_log = new BalanceLog();
            $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::fkassa, null, $comment, null, null, false, $order->payway_id);
            $balanse_log->save();

            $order->system_payment_id = $post['m_operation_id'];
            $order->completed = true;
            $order->save();
            echo 200;
        }
        else
        {
            echo 302;
        }

        $this->redirect(['/user/history']);
        //return redirect('/transactions');
    }

    public function actionFreeobmen()
    {
        LogMy::getInstance()->setLog(['method' => 'actionFreeobmen', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'freeobmen');
        $post = $_POST;
        if (empty($post)) {
            return  $this->redirect(['/user/history']);
        }


        if(!isset($post['sign']) OR !isset($post['hash']))
        {
            return  $this->redirect(['/user/history']);
        }

        $merchant = FreeObmen::getInstance();

        $unique_code = substr($post['orderId'], 0, -2);

        if (!$order = PaymentLog::findIdentity($unique_code) OR $order->payment_system != BalanceLog::freeobmen OR $order->completed == true) {
            return $this->redirect(['/user/history']);
        }

        $user = User::findIdentity($order->user_id);

        if (!$user) {
            return $this->redirect(['/user/history']);
        }

        $Secret  = $merchant->secret;
        $HashCod = md5($post['hash'] . $Secret);

        if($post['sign'] == $HashCod)
        {
            $comment = 'Пополнение через FreeObmen';
            $balanse_log = new BalanceLog();
            $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::freeobmen, null, $comment, null, null, false, $order->payway_id);
            $balanse_log->save();

            $order->system_payment_id = $post['paymentId'];
            $order->completed = true;
            $order->save();
        }
        $this->redirect(['/user/history']);
    }

    public function actionPayinSuccess()
    {
        LogMy::getInstance()->setLog(['method' => 'actionPayinSuccess', 'post' => $_POST, 'get' => $_GET, 'ip' => self::getIP()], 'payinpayout');
        $this->redirect(['/user/history']);
        $post = $_POST;
        if (empty($post)) {
            return $this->redirect(['/user/history']);
        }

        if(!isset($post['sign']) OR !isset($post['paymentStatus']) OR $post['paymentStatus'] != 1)
        {
            return $this->redirect(['/user/history']);
        }

        $merchant = PayinPayout::getInstance();

        if (!$order = PaymentLog::findIdentity( $post['orderId']) OR $order->payment_system != BalanceLog::payinpayout OR $order->completed == true) {
            return $this->redirect(['/user/history']);
        }

        $user = User::findIdentity($order->user_id);

        if (!$user) {
            return $this->redirect(['/user/history']);
        }

        $arHash = array(
            $merchant->agentId,
            $post['orderId'],
            $post['paymentId'],
            $post['amount'],
            $post['phone'],
            $post['paymentStatus'],
            $post['paymentDate'],
        );

        $sign = $merchant->getHesh($arHash);

        if($post['sign'] == $sign)
        {
            $comment = 'Пополнение через PayinPayout';
            $balanse_log = new BalanceLog();
            $balanse_log->addLog($user->id, $order->size, BalanceLog::deposit, BalanceLog::in_process, BalanceLog::payinpayout, null, $comment, null, null, false, $order->payway_id);
            $balanse_log->save();

            $order->system_payment_id = $post['paymentId'];
            $order->completed = true;
            $order->save();
        }
        return $this->redirect(['/user/history']);
    }

}
