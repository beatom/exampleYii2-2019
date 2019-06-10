<?php

namespace console\controllers;

use Codeception\Template\Api;
use common\models\DaysLog;
use common\models\Events;
use common\models\LogConfirm;
use common\models\BalanceLog;
use common\models\BonusDebt;
use common\models\Chat;
use common\models\investments\InvestmentsCron;
use common\models\logs\SendPulseNotification;
use common\models\Options;
use common\models\Overdraft;
use common\models\PaymentLog;
use common\models\QueueMail;
use common\models\trade\InvestmentDailyLog;
use common\models\trade\InvestmentProtection;
use common\models\trade\RequestLeverage;
use common\models\trade\SolutionTrading;
use common\models\trade\TradingAccountBalanceHistoryDay;
use common\models\trade\TradingAccountChangeHistory;
use common\models\trade\TradingAccountDeleteRequest;
use common\models\trade\TradingAccountHistoryTerminal;
use common\models\trade\TradingAccountHistoryTerminal2;
use common\models\trade\TradingAccountYieldLog;
use common\models\User;
use common\models\UserIpLog;
use common\models\WebinarArchive;
use common\service\api\AmoCrm;
use common\service\api\SendPulse;
use common\service\api_terminal\Payop;
use common\service\PartnerProgram;
use common\service\PusherService;
use common\service\Servis;
use Symfony\Component\Console\Terminal;
use yii\console\Controller;
use common\models\ChatMessage;
use common\models\UserPartnerInfo;
use common\models\trade\TradingAccount;
use common\models\trade\InvestmentLog;
use common\models\trade\TradingPeriodLog;
use common\models\trade\TradingOffer;
use common\models\trade\Investment;
use common\models\trade\InvestmentDebt;
use common\models\PartnerBasicIncome;
use common\models\BalanceBonusLog;
use common\service\api_terminal\ApiTerminal;
use common\models\Queue;
use Yii;
use common\models\EmailTemplate;
use common\models\trade\Solution;
use common\models\trade\SolutionPeriodLog;
use common\models\trade\SolutionDailyLog;
use common\models\trade\TradingAccountDuStatistic;
use yii\helpers\Url;
use common\models\trade\InvestmentDeleteLog;
use common\service\CBRAgent;
use common\models\Currencies;
use common\models\PaymentCardRequest;
use common\models\ManagerCard;
use common\service\LogMy;
use DateTimeZone;
use common\service\api_terminal\Bankcomat;
use common\models\SmsBlock;

class CronController extends Controller
{
      public function actionWorkQueueMail()
    {
        echo 'start cron' . PHP_EOL;

        $res = QueueMail::findByNotWorket();
        $worked_ids = [];
        foreach ($res as $item) {
            $worked_ids[] = $item->id;
        }
        if (empty($worked_ids)) {
            echo 'нет задач' . PHP_EOL;
            return;
        }
        QueueMail::setWorked($worked_ids);

        foreach ($res as $item) {

            if ('adminMessage' == $item['template']) {
                $res = Yii::$app
                    ->mailer
                    ->compose(
                        ['html' => $item['template'] . '-html', 'text' => $item['template'] . '-text'],
                        ['message' => unserialize($item['message'])]
                    )
                    ->setFrom($item['from'])
                    ->setTo($item['to'])
                    ->setSubject($item['subject'])
                    ->send();
            } else if (is_numeric($item['template'])) {
                $email_template = EmailTemplate::findIdentity($item['template']);
                $res = $email_template->getEmailTemplate(unserialize($item['message']));
                $email_template->sendMail($item['to'], $res['title'], $res);
            }
        }
    }

    public function actionWorkQueue()
    {
        echo 'start cron' . PHP_EOL;

        $res = Queue::findByNotWorket();
        $worked_ids = [];
        foreach ($res as $item) {
            $worked_ids[] = $item->id;
        }
        if (empty($worked_ids)) {
            echo 'нет задач' . PHP_EOL;
            return;
        }

        Queue::setWorked($worked_ids);

        foreach ($res as $item) {
            $task = $item->task;

            echo $task . PHP_EOL;

            if ($task == 'actionGetHistoryTerminal') {
                $this->$task($item->type, $item->params);
                continue;
            }
             if ($item->type) {
                $this->$task($item->type);
            } else {
                $this->$task();
            }
        }
    }

    public function actionChangeStatus()
    {
        echo 'actionChangeStatus' . PHP_EOL;
        PartnerProgram::getInstance()->ChangeStatus();
    }


    public function actionUnreadMessagesNotification()
    {
        ChatMessage::sendUnreadMessagesNotification();
    }

    public function actionTest()
    {
        $user = User::findIdentity(24218);
//        Payop::getInstance()->paymentMethods();
        Payop::getInstance()->getForm($user, 100);
    }

    public function actionCreateUsers()
    {
        $i = 0;
        $user = new User();
        $user = $user->createUser('partner_test'.++$i, 'partner_test'.$i .'@test.ru', '111111');
        $user->date_reg = date('Y-m-d H:i:s', strtotime(' -' . (20-$i) . ' seconds'));
        $user->save();
        $prev_user_id = $user->id;

        $user = new User();
        $user = $user->createUser('partner_test'.++$i, 'partner_test'.$i .'@test.ru', '111111');
        $user->partner_id = $prev_user_id;
        $user->balance = $i * 500;
        $user->date_reg = date('Y-m-d H:i:s', strtotime(' -' . (20-$i) . ' seconds'));
        $user->save();
        $prev_user_id = $user->id;
        BalanceLog::add($user->id, $user->balance, 0, 1, 0, null, 'Тестовое пополнение для партнерской программы');

        $user = new User();
        $user = $user->createUser('partner_test'.++$i, 'partner_test'.$i .'@test.ru', '111111');
        $user->partner_id = $prev_user_id;
        $user->status_open = date('Y-m-d H:i:s');
        $user->balance = $i * 500;
        $user->date_reg = date('Y-m-d H:i:s', strtotime(' -' . (20-$i) . ' seconds'));
        $user->save();
        $prev_user_id = $user->id;
        BalanceLog::add($user->id, $user->balance, 0, 1, 0, null, 'Тестовое пополнение для партнерской программы');

        $user = new User();
        $user = $user->createUser('partner_test'.++$i, 'partner_test'.$i .'@test.ru', '111111');
        $user->partner_id = $prev_user_id;
        $user->balance = $i * 1000;
        $user->date_reg = date('Y-m-d H:i:s', strtotime(' -' . (20-$i) . ' seconds'));
        $user->save();
        $prev_user_id = $user->id;
        BalanceLog::add($user->id, $user->balance, 0, 1, 0, null, 'Тестовое пополнение для партнерской программы');

        $user = new User();
        $user = $user->createUser('partner_test'.++$i, 'partner_test'.$i .'@test.ru', '111111');
        $user->partner_id = $prev_user_id;
        $user->balance = $i * 500;
        $user->date_reg = date('Y-m-d H:i:s', strtotime(' -' . (20-$i) . ' seconds'));
        $user->save();

        BalanceLog::add($user->id, $user->balance, 0, 1, 0, null, 'Тестовое пополнение для партнерской программы');

        $user = new User();
        $user = $user->createUser('partner_test'.++$i, 'partner_test'.$i .'@test.ru', '111111');
        $user->partner_id = $prev_user_id;
        $user->balance = $i * 100;
        $user->date_reg = date('Y-m-d H:i:s', strtotime(' -31 days'));
        $user->save();
        BalanceLog::add($user->id, $user->balance, 0, 1, 0,  date('Y-m-d H:i:s', strtotime(' -31 days')), 'Тестовое пополнение для партнерской программы');

        $this->actionPartnerTrees();
        $day = DaysLog::getLog();

        $day->sum_start =  User::find()->where('balance >= 17')->sum('balance');
        $day->save();
    }

    public function actionPartnerTrees() {

        foreach (User::find()
                     ->where('id IN (SELECT DISTINCT(partner_id) FROM user WHERE partner_id IS NOT NULL)')
                     ->all() as $user) {
            UserPartnerInfo::tree($user->id);
            PartnerProgram::isChangeStatus($user);
        }
    }

    public function actionSetManagersCards()
    {
        ManagerCard::setManagers();
    }

    /**
     * обновление курсов валют
     */
    public function actionUpdateCurrencies()
    {
        Currencies::updateRates();
    }

    public function actionUsernames()
    {
        foreach (User::find()->all() as $user) {
            if (!preg_match("/^[a-z0-9_-]+$/i", $user->username)) {
                $user->edit_username = 1;
                $user->save();
            }
        }
    }

    public function actionUseravatars()
    {
        foreach (User::find()->all() as $user) {
            $user->avatar = null;
            $user->save();
            $user->generateLetterAvatar();
        }
    }


    public function actionReceiveBankomatPayments($days = 1)
    {
        $this->actionCloseSolutionInvestments();
        $this->actionSendBankcomatPay();
        $api = Bankcomat::getInstance();

        foreach (PaymentLog::find()->where(['completed' => false])->andWhere("date_add >= '" . date('Y-m-d H:i:s', strtotime(' -' . $days . ' days')) . "'")->all() as $l) {
            $answer = $api->query("order-get", [
                "order_id" => $l->system_payment_id
            ]);

            if ($answer AND $answer->status == 'completed') {
                $l->completed = true;
                $l->save();

                $user = User::findIdentity($l->user_id);
                $summ_user = Overdraft::closeDolg($user->id, $l->size);
                $user->balance += $summ_user;
                $res = $user->save();

                if ($res) {
                    $user->giveFirstBonus();
                    $comment = 'Пополнение через bankcomat';
                    $balanse_log = new BalanceLog();
                    $balanse_log->addLog($user->id, $l->size, BalanceLog::deposit, BalanceLog::done, BalanceLog::bankcomat, null, $comment, null, null, false, $l->payway_id);
                    if ($balanse_log->save()) {
                        BonusDebt::payOutUserDebts($user->id);
                    }
                }
            }
        }
    }

    public function actionSetUsersPromo()
    {
        foreach (User::find()->all() as $user) {
            $user->setInvationCodes();
        }
    }

    public function actionCountDays() {
        foreach (DaysLog::find()->all() as $log) {
            $log->count();
        }
    }

    public function actionPayMoney() {
        DaysLog::getLog();
        DaysLog::getLog(date('Y-m-d', strtotime('yesterday')));
        DaysLog::getLog(date('Y-m-d', strtotime('tomorrow')));
        DaysLog::payMoney();
        User::setEventsNotice();
        $this->actionClearCache();
    }

    public function actionPayout()
    {
        $date = date('Y-m-d H:i:s');
        foreach (BalanceLog::find()->where(['execution_time' => null, 'status' => 4])->with('user')->all() as $bl) {
            $user = $bl->user;
            $user->balance += abs($bl->summ);
            $user->save();

            $bl->status = 1;
            $bl->execution_time = $date;
            $bl->save();
        }
    }

    public function actionStartPeriod() {
        $this->actionPayout();
        $log = DaysLog::getLog();
        $log->sum_start = User::find()->where('balance >= 17')->sum('balance');
        $log->sum_end = $log->sum_start;
        $log->save();
        $this->actionUpdateNotice();
    }


    public function actionUpdateNotice() {
        User::setEventsNotice();
    }

    public function actionSendPulseNotifications() {
        foreach (User::find()
                     ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status IN (1,4) AND operation IN (0,3) GROUP BY user_id) as bl1 on bl1.user_id = user.id')
                     ->leftJoin('(SELECT id as lgid, user_id FROM sendpulse_notifications WHERE notification = 2 GROUP BY user_id) as sp1 on sp1.user_id = user.id')
                     ->where('date_reg <= "' . date('Y-m-d H:i:s', strtotime('-3 days')) . '" AND sm1 IS NULL AND lgid is NULL')
                    ->all() as $u) {
            SendPulseNotification::send($u->id, SendPulseNotification::three_days, [
                'email' => $u->email,
                'phone' => $u->phone,
                'name' => $u->firstname ? $u->firstname : $u->username,
            ]);
        }

        foreach (User::find()
                     ->leftJoin('(SELECT SUM(summ) as sm1, user_id FROM balance_log WHERE status IN (1,4) AND operation IN (0,3) GROUP BY user_id) as bl1 on bl1.user_id = user.id')
                     ->leftJoin('(SELECT id as lgid, user_id FROM sendpulse_notifications WHERE notification = 3 GROUP BY user_id) as sp1 on sp1.user_id = user.id')
                     ->where('date_reg <= "' . date('Y-m-d H:i:s', strtotime('-15 days')) . '" AND sm1 IS NULL AND lgid is NULL')
                     ->all() as $u) {
            SendPulseNotification::send($u->id, SendPulseNotification::fifteen_days, [
                'email' => $u->email,
                'phone' => $u->phone,
                'name' => $u->firstname ? $u->firstname : $u->username,
            ]);
        }
    }

    public function actionClearCache() {
        Yii::$app->cache->cachePath .= '/../../../frontend/runtime/cache';
//        var_dump(Yii::$app->cache);
        Yii::$app->cache->delete('statistic_home');
        Yii::$app->cache->delete('statistic_cabinet');
        Yii::$app->cache->getOrSet('statistic_home', function () {
            return DaysLog::getTable(true);
        });
        Yii::$app->cache->getOrSet('statistic_cabinet', function () {
            return DaysLog::getTable();
        });
    }


}