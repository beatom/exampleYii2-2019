<?php
namespace backend\controllers;

use common\models\Objective;
use common\models\ObjectiveStage;
use common\models\PaymentSystems;
use common\models\PaymentSystemsWithdraw;
use common\models\User;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use backend\models\SetingForm\SetingHomePageForm;
use common\models\Options;
use backend\models\SetingForm\SocialForm;
use backend\models\SetingForm\TradeStaticPageForm;
use backend\models\SetingForm\PartnershipForm;
use common\models\investments\InvestmentsPlan;
use backend\models\SetingForm\PlanSingleForm;
use common\models\trade\TraidingPlan;
use backend\models\SetingForm\PlanTradeSingleForm;
use backend\models\SetingForm\AboutPageForm;
use common\models\ManagerCard;
use yii\data\Pagination;

/**
 * Site controller
 */
class SetingController extends Controller
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
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->request->post()) {
            if (!empty($_POST['ball_rate'])) {
                Options::setOptionValueByKey('exchange_rate', '1|' . $_POST['ball_rate']);
            }
            if (!empty($_POST['yandex_metrica'])) {
                Options::setOptionValueByKey('yandex_metrica', $_POST['yandex_metrica']);
            }
            if (!empty($_POST['jivosite_code'])) {
                Options::setOptionValueByKey('jivosite_code', $_POST['jivosite_code']);
            }
            if (!empty($_POST['card_number'])) {
                Options::setOptionValueByKey('deposit_card_number', $_POST['card_number']);
            }
        }
        $card_number = Options::getOptionValueByKey('deposit_card_number');
        $yandex_metrica = Options::getOptionValueByKey('yandex_metrica');
        $jivosite_code = Options::getOptionValueByKey('jivosite_code');
        $exchange_rate = explode('|', Options::getOptionValueByKey('exchange_rate'));
        return $this->render('index', [
            'card_number' => $card_number,
            'exchange_rate' => $exchange_rate,
            'yandex_metrica' => $yandex_metrica,
            'jivosite_code' => $jivosite_code
        ]);
    }

    /**
     * @return string
     */
    public function actionHomePage()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $options = Options::getOptions(array_merge(Options::keys_home_page_ru, Options::keys_home_page_en));

        $model = new SetingHomePageForm();
        $model->setData($options);
        $is_save = false;
        if ($model->load(Yii::$app->request->post())) {
            $model->save($options);
            $is_save = true;
        }

        return $this->render('home-page', [
            'model' => $model,
            'is_save' => $is_save,
        ]);
    }

    public function actionManagerCards()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $query = ManagerCard::find();

        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('manager-cards', [
            'models' => $models,
        ]);
    }

    public function actionToggle_manager_card($id)
    {
        if ($model = ManagerCard::findIdentity($id)) {
            if ($model->is_main) {
                $model->is_main = false;
                $model->save();
            } else {
                ManagerCard::updateAll(['is_main' => false]);
                $model->is_main = true;
                $model->save();
            }
            Yii::$app->getSession()->setFlash('success', 'Основная карточка успешно обновлена');
        }
        $this->redirect(['/seting/manager-cards']);
    }

    public function actioAddManagerCard()
    {
        $model = ManagerCard::find();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->edit()) {
                Yii::$app->getSession()->setFlash('success', 'Карточка менеджера успешно добавлена');
            }
        }

        return $this->render('manager-card', [
            'models' => $model,
        ]);
    }

    public function actionManagerCard($id)
    {
        if (!$model = ManagerCard::findIdentity($id)) {
            $this->redirect(['/seting/manager-cards']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->edit()) {
                Yii::$app->getSession()->setFlash('success', 'Карточка менеджера успешно изменена');
            }
        }

        return $this->render('manager-card', [
            'model' => $model,
        ]);
    }

    public function actionAddManagerCard()
    {
        $model = new ManagerCard();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->edit()) {
                Yii::$app->getSession()->setFlash('success', 'Карточка менеджера успешно создана');
                return $this->redirect('/seting/manager-card/' . $model->id);
            }
        }

        return $this->render('manager-card', [
            'model' => $model,
        ]);
    }

    public function actionDeleteManagerCard($id)
    {
        if (!$model = ManagerCard::findIdentity($id)) {
            $this->redirect(['/seting/manager-cards']);
        }

        $model->delete();
        ManagerCard::setManagers();
        Yii::$app->getSession()->setFlash('success', 'Карточка менеджера успешно удалена, менеджеры пользователей были перероспределены');


        return $this->redirect(['/seting/manager-cards']);
    }


    public function actionTrade_plan()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $models = TraidingPlan::findPlans();
        return $this->render('pages/trade_plans', [
            'models' => $models,
        ]);
    }


    public function actionEditTradePlan($id)
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $plan = TraidingPlan::findIdentity($id);

        if (!$plan) {
            $this->redirect((['/seting/trade_plans']));
        }

        $model = new PlanTradeSingleForm();
        $model->setData($plan);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save($plan)) {
                $this->redirect(['/seting/trade_plan']);
            }
        }

        return $this->render('pages/trade_plans_edit', [
            'model' => $model,
            'seo' => ['title' => 'Редактировать план',
                'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }


    public function actionInvest()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $models = InvestmentsPlan::findPlans();
        return $this->render('pages/investments_plans', [
            'models' => $models,
        ]);
    }

    public function actionEditPlan($id)
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $plan = InvestmentsPlan::findIdentity($id);

        if (!$plan) {
            $this->redirect((['/seting/invest']));
        }

        $model = new PlanSingleForm();
        $model->setData($plan);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save($plan)) {
                $this->redirect(['/seting/invest']);
            }
        }

        return $this->render('pages/investments_plans_edit', [
            'model' => $model,
            'seo' => ['title' => 'Редактировать план',
                'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }

    public function actionAbout()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $options = Options::getOptions(Options::keys_about);

        $model = new AboutPageForm();
        $model->setData($options);
        $is_save = false;
        if ($model->load(Yii::$app->request->post())) {
            $model->save($options);
            $is_save = true;
        }

        return $this->render('about', [
            'model' => $model,
            'is_save' => $is_save,
        ]);
    }

    public function actionPartnership()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $options = Options::getOptions(array_merge(Options::keys_partnership_ru, Options::keys_partnership_en));

        $model = new PartnershipForm();
        $model->setData($options);
        $is_save = false;
        if ($model->load(Yii::$app->request->post())) {
            $model->save($options);
            $is_save = true;
        }

        return $this->render('pages/partnership', [
            'model' => $model,
            'is_save' => $is_save,
        ]);
    }

    public function actionTrade()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $options = Options::getOptions(array_merge(Options::keys_trade_static_page_ru, Options::keys_trade_static_page_en));

        $model = new TradeStaticPageForm();
        $model->setData($options);
        $is_save = false;
        if ($model->load(Yii::$app->request->post())) {
            $model->save($options);
            $is_save = true;
        }

        return $this->render('trade', [
            'model' => $model,
            'is_save' => $is_save,
        ]);
    }

    public function actionSocial()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $options = Options::getOptions(Options::keys_social);

        $model = new SocialForm();
        $model->setData($options);
        $is_save = false;
        if ($model->load(Yii::$app->request->post())) {
            $model->save($options);
            $is_save = true;
        }

        return $this->render('social', [
            'model' => $model,
            'is_save' => $is_save,
        ]);
    }

    public function actionTesting()
    {
//        foreach (User::find()->where('avatar IS NULL')->all() as $u) {
//            $u->generateLetterAvatar();
//        }
    }

    public function actionMailTest()
    {
        foreach (User::find()->where('avatar IS NULL')->all() as $u) {
            $u->generateLetterAvatar();
        }
    }

    public function actionHistory_duplicates()
    {
        $request = Yii::$app->request;
        $check = $request->get('check');
        if ($check == 1) {
            $result1 = [];
            $dublicates1 = Yii::$app->db->createCommand('SELECT id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP, COUNT(*) AS duplicates
                        FROM trading_account_history_terminal
                        GROUP BY id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP
                        HAVING duplicates > 1')
                ->queryAll();
            if (!empty($dublicates1)) {
                foreach ($dublicates1 as $d) {
                    $r['trading_account_id'] = $d['id_trading'];
                    $r['close_date'] = $d['CLOSE_DATE'];
                    $result1[] = $r;
                }
                Options::setOptionValueByKey('terminal_history_duplicate_1', serialize($result1));
            } else {
                Options::setOptionValueByKey('terminal_history_duplicate_1', false);
            }
        }
        if ($check == 2) {
            $result2 = [];
            $dublicates2 = Yii::$app->db->createCommand('SELECT id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP, COUNT(*) AS duplicates
                        FROM trading_account_history_terminal_2
                        GROUP BY id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP
                        HAVING duplicates > 1')
                ->queryAll();
            if (!empty($dublicates2)) {
                foreach ($dublicates2 as $d) {
                    $r['trading_account_id'] = $d['id_trading'];
                    $r['close_date'] = $d['CLOSE_DATE'];
                    $result2[] = $r;
                }
                Options::setOptionValueByKey('terminal_history_duplicate_2', serialize($result2));
            } else {
                Options::setOptionValueByKey('terminal_history_duplicate_2', false);
            }
        }
        $dublicate_1 = unserialize(Options::getOptionValueByKey('terminal_history_duplicate_1'));
        $dublicate_2 = unserialize(Options::getOptionValueByKey('terminal_history_duplicate_2'));
        $dublicate_code = Options::getOptionValueByKey('terminal_duplicate_search');
        return $this->render('history_duplicates', [
            'dublicate_1' => $dublicate_1,
            'dublicate_2' => $dublicate_2,
            'dublicate_code' => $dublicate_code,
        ]);
    }

    public function actionObjectives()
    {

        $model = new Objective();

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            $model->save();
            $model->max_sum = 0;
            Yii::$app->getSession()->setFlash('success', 'Новая цель успешно добавлена');
        }

        $query = Objective::find()
            ->orderBy('max_sum ASC');

        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 30]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();


        return $this->render('objectives', [
            'models' => $models,
            'model' => $model,
            'pages' => $pages,
        ]);
    }

    public function actionDelete_objective($id)
    {
        if ($model = Objective::findIdentity($id)) {
            ObjectiveStage::deleteAll(['objective_id' => $id]);
            $model->delete();
            Yii::$app->getSession()->setFlash('success', 'Цель успешно удалена');
        }
        return $this->redirect(['/seting/objectives']);
    }

    public function actionEdit_objective($id)
    {
        if (!$model = Objective::findIdentity($id)) {
            return $this->redirect(['/seting/objectives']);
        }
        $models = ObjectiveStage::find()->where(['objective_id' => $id])->orderBy('stage ASC')->all();
        return $this->render('objective', [
            'model' => $model,
            'models' => $models
        ]);
    }

    public function actionAdd_objective_stage($id)
    {
        if (!$objective = Objective::findIdentity($id)) {
            return $this->redirect(['/seting/objectives']);
        }
        $model = new ObjectiveStage();

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            if (ObjectiveStage::find()->where(['objective_id' => $id, 'stage' => $model->stage])->exists()) {
                $model->addError('stage', 'У данной цели уровень с таким процентом уже существует');
            } else {
                $model->objective_id = $id;
                $model->save();
                Yii::$app->getSession()->setFlash('success', 'Новый уровень успешно добавлен');
                return $this->redirect(['/seting/edit_objective/' . $id]);
            }
        }
        return $this->render('objective_stage', [
            'model' => $model,
            'objective' => $objective,
        ]);
    }

    public function actionDelete_objective_stage($id)
    {
        var_dump('fuck');
        die;
        if ($model = ObjectiveStage::findIdentity($id)) {
            $objective = Objective::findIdentity($model->objective_id);
            $model->delete();
            Yii::$app->getSession()->setFlash('success', 'Уровень успешно удален');
            if ($objective) {
                return $this->redirect(['/seting/edit_objective/' . $objective->id]);
            }
        }
        return $this->redirect(['/seting/objectives']);
    }

    public function actionEdit_objective_stage($id)
    {
        if (!$model = ObjectiveStage::findIdentity($id)) {
            return $this->redirect(['/seting/objectives']);
        }

        $objective = Objective::findIdentity($model->objective_id);
        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {

            if (ObjectiveStage::find()->where(['objective_id' => $id, 'stage' => $model->stage])->andWhere('id <> ' . $id)->exists()) {
                $model->addError('stage', 'У данной цели уровень с таким процентом уже существует');
            } else {
                $model->save();
                Yii::$app->getSession()->setFlash('success', 'Уровень успешно изменен');
                return $this->redirect(['/seting/edit_objective_stage/' . $id]);
            }
        }
        return $this->render('objective_stage', [
            'model' => $model,
            'objective' => $objective,
        ]);
    }

    public function actionSortAccountsPositions()
    {

        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            // $positions = $request->post('positions');
            $ids = $request->post('ids');
            $payment_class = $request->post('is_deposit') ? PaymentSystems::class : PaymentSystemsWithdraw::class;
            $data['status'] = 'error';
            if (!$ids) {
                return json_encode($data);
            }

            foreach ($ids as $key => $value) {
                $payment_class::updateAll(['position' => $key], ['id' => $value]);
            }

            $data['status'] = 'Ok';
            return json_encode($data);
        }
    }

    public function actionPayment_toggle()
    {

        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $payment_class = $request->post('is_deposit') ? PaymentSystems::class : PaymentSystemsWithdraw::class;
            $id = $request->post('id');
            $data['status'] = 'error';
            if (!$system = $payment_class::findIdentity($id)) {
                return json_encode($data);
            }
            $system->show = !$system->show;
            $system->save();
            $data['status'] = 'Ok';
            return json_encode($data);
        }
    }

    public function actionPayments()
    {
        $systems = PaymentSystems::getSystems(false);

        return $this->render('payments', [
            'systems' => $systems,
        ]);
    }

    public function actionWithdraws()
    {
        $systems = PaymentSystemsWithdraw::getSystems(false);

        return $this->render('withdraws', [
            'systems' => $systems,
        ]);
    }

    public function actionPayment($id)
    {
        if (!$model = PaymentSystems::findIdentity($id)) {
            return $this->redirect(['payments']);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            if ($model->sum_max <= $model->sum_min) {
                $model->addError('sum_max', 'Максимальная сумма пополнения должна быть больше минимальной');
            }
            if (!$model->errors) {
                if ($model->save()) {
                    Yii::$app->getSession()->setFlash('success', 'Платежная система успешно обновлена');
                } else {
                    var_dump($model->errors);
                    die;
                }
            }


        }

        return $this->render('payment', [
            'model' => $model,
        ]);
    }

    public function actionWithdraw($id)
    {
        if (!$model = PaymentSystemsWithdraw::findIdentity($id)) {
            return $this->redirect(['withdraws']);
        }

        if ($model->load(Yii::$app->request->post()) AND $model->validate()) {
            if ($model->sum_max <= $model->sum_min) {
                $model->addError('sum_max', 'Максимальная сумма вывода должна быть больше минимальной');
            }
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Платежная система успешно обновлена');
            } else {
                var_dump($model->errors);
                die;
            }


        }

        return $this->render('withdraw', [
            'model' => $model,
        ]);
    }


}
