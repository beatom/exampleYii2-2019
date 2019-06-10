<?php
namespace backend\controllers;

use common\models\BalanceBonusLog;
use common\models\BonusDebt;
use common\models\ChatMessage;
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
class ChatController extends Controller
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
                        'roles' => ['moderator'],
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
        $query = ChatMessage::find()->where(['deleted_at' => null])->with(['user','childs'])->orderBy('date_add DESC');

        if (!empty($_GET['text'])) {
            $query->andFilterWhere(['like', 'text_like', $_GET['text_like']]);
        }
        if (!empty($_GET['user'])) {
            $user_search = trim($_GET['user']);
            $query->leftJoin('user as u', 'u.id = chat_message.user_id')
                    ->andWhere("u.id = '$user_search' OR username LIKE '$user_search' OR email LIKE '$user_search'");
        }

        if (!empty($_GET['date_from'])) {
            $query->andFilterWhere(['<=', 'date_add', $_GET['date_from']]);
        }
        if (!empty($_GET['date_to'])) {
            $query->andFilterWhere(['>=', 'date_add', $_GET['date_to']]);
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
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    public function actionEdit($id)
    {
        if (!$review = ChatMessage::findIdentity($id)) {
            return $this->redirect('/chat/index');
        }

        if ($review->load(Yii::$app->request->post())) {
            if ($review->saveMessage()) {
                Yii::$app->getSession()->setFlash('success', 'Сообщение успешно изменено');
                return $this->redirect('/chat/edit/' . $review->id);
            }
        }

        $review_tree = ChatMessage::find()->where(['deleted_at' => null, 'parent_id' => $id])->orderBy('date_add DESC')->all();
        $review_tree = !empty($review_tree) ? $review_tree : [];

        return $this->render('message', [
            'model' => $review,
            'review_tree' => $review_tree,
            'title' => $review->parent_id ? 'Редактирование отзыва id:'.$review->id . ', Ответ к отзыву id:'.$review->parent_id : 'Редактирование отзыва id:'.$review->id
        ]);
    }

    public function actionAdd($parent_id = false)
    {
        $review = new ChatMessage();
        if($parent_id AND !ChatMessage::findIdentity($parent_id)) {
            return $this->redirect('/chat/index/');
        }
        $review->parent_id = $parent_id;
        $review->likes = 0;
        $review->dislikes = 0;
        if ($review->load(Yii::$app->request->post())) {
            if ($review->saveMessage()) {
                Yii::$app->getSession()->setFlash('success', 'Сообщение успешно Изменено');
                return $this->redirect('/chat/edit/' . $review->id);
            }

        }
        return $this->render('message', [
            'model' => $review,
            'review_tree' => [],
            'title' => $review->parent_id ? 'Ответ к отзыву id:'.$review->parent_id : 'Добавление отзыва',
        ]);
    }

    public function actionDelete($id)
    {
        ChatMessage::deleteMessage($id, Yii::$app->user->id);
        Yii::$app->getSession()->setFlash('success', 'Сообщения успешно удалены');
        return $this->redirect(['/chat/index']);

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
