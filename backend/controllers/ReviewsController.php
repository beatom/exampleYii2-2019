<?php
namespace backend\controllers;

use common\models\BalanceBonusLog;
use common\models\BonusDebt;
use common\models\Country;
use common\models\Overdraft;
use common\models\PartnerBaluLog;
use common\models\trade\Investment;
use common\models\trade\TradingAccount;
use common\models\User;
use common\models\UserDoc;
use common\models\UserIpLog;
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
use common\models\Review;
/**
 * Site controller
 */
class ReviewsController extends Controller
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

        $query = ManagerReviews::find()->orderBy('date_add DESC');

        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionEdit($id)
    {
        if (!$review = ManagerReviews::findIdentity($id)) {
            return $this->redirect('/reviews/index');
        }

        if ($review->load(Yii::$app->request->post())) {


            $errors = 0;
            if (!$user = User::findIdentity($review->user_id)) {
                $errors++;
                $review->addError('user_id', 'Пользователя с таким id не существует');
            }
        

            if (Yii::$app->request->post()['ManagerReviews']['date_add']) {
                $review->date_add = date('Y-m-d H:i:s', strtotime(Yii::$app->request->post()['ManagerReviews']['date_add']));
            } elseif (!$review->date_add) {
                $review->date_add = date('Y-m-d H:i:s');
            }


            if (!$errors AND $review->validate()) {
                $review->save();
                Yii::$app->getSession()->setFlash('success', 'Отзыв успешно изменен');
                return $this->redirect('/reviews/edit/' . $review->id);
            }
        }

        return $this->render('review', [
            'model' => $review,
            'title' => 'Редактирование отзыва'
        ]);
    }

    public function actionAdd()
    {
        $review = new ManagerReviews();

        if ($review->load(Yii::$app->request->post())) {
            $errors = 0;
            if (!$user = User::findIdentity($review->user_id)) {
                $errors++;
                $review->addError('user_id', 'Пользователя с таким id не существует');
            }
            if (Yii::$app->request->post()['ManagerReviews']['date_add']) {
                $review->date_add = date('Y-m-d H:i:s', strtotime(Yii::$app->request->post()['ManagerReviews']['date_add']));
            } elseif (!$review->date_add) {
                $review->date_add =  date('Y-m-d H:i:s');
            }
     

            if (!$errors AND $review->validate()) {
                $review->save();
                Yii::$app->getSession()->setFlash('success', 'Отзыв успешно добавлен');
                return $this->redirect('/reviews/edit/' . $review->id);
            }

        }

        return $this->render('review', [
            'model' => $review,
            'title' => 'Добавление отзыва'
        ]);
    }

    public function actionDelete($id)
    {
        if ($review = ManagerReviews::findIdentity($id)) {
            $review->delete();
            Yii::$app->getSession()->setFlash('success', 'Отзыв успешно удален');
        }
        return $this->redirect('/reviews/index');

    }


    public function actionGet_periods()
    {

        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $account_id = $request->post('account_id');

            $periods_objects = TradingPeriodLog::find()->where(['trading_account_id' => $account_id])->orderBy('date_start DESC')->all();
            $data['status'] = 'Empty';
            $data['periods'] = '';
            if ($periods_objects) {
                $data['status'] = 'Ok';
                $periods = [];
                foreach ($periods_objects as $p) {
                    $period_el = [];
                    $period_el['id'] = $p->id;
                    $period_el['string'] = date('d.m', strtotime($p->date_start)) . ' - ' . date('d.m.y', strtotime($p->date_end));
                    $period_el['start'] = date('Y-m-d', strtotime($p->date_start));
                    $period_el['end'] = date('Y-m-d', strtotime($p->real_date_end));
                    $periods[] = $period_el;
                }
                $data['periods'] = $periods;
            }
            return json_encode($data);
        }
    }

    public function actionChat()
    {
        $query = Review::find()->where(['parent_id' => null])->orderBy('date_add DESC');

        $countQuery = clone $query;

        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('chat', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionChatAdd()
    {
        $review = new Review();

        if ($review->load(Yii::$app->request->post())) {
            if ( $review->validate()) {
                $review->save();
                Yii::$app->getSession()->setFlash('success', 'Отзыв успешно добавлен');
                return $this->redirect('/reviews/chat-edit/' . $review->id);
            }
        }
        $review_tree = [];

        return $this->render('chat-edit', [
            'model' => $review,
            'review_tree' => $review_tree,
            'title' => 'Добавление нового отзыва'
        ]);
    }


    public function actionChatEdit($id)
    {
        if(!$review = Review::findIdentity($id)) {
            Yii::$app->getSession()->setFlash('warning', 'Отзыв не найден изменен');
            return $this->redirect('/review/chat');
        }

        if ($review->load(Yii::$app->request->post())) {
            if ( $review->validate()) {
                $review->save();
                Yii::$app->getSession()->setFlash('success', 'Отзыв успешно изменен');
            }
        }

        $review_tree = Review::find()->where(['show' => true, 'parent_id' => $id])->orderBy('date_add DESC')->all();
        $review_tree = !empty($review_tree) ? $review_tree : false;

        $parent = $review->parent_id ? Review::findIdentity($review->parent_id) : $review;
        return $this->render('chat-edit', [
            'parent' => $parent,
            'review_tree' => $review_tree,
            'model' => $review,
            'title' => $review->parent_id ? 'Отзыв id:'.$id.', Ответ к отзыву id:'.$review->parent_id : 'Отзыв id:'.$id,
        ]);
    }
    


}
