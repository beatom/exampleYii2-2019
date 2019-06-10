<?php
namespace backend\controllers;

use common\models\User;
use backend\models\MessageUserForm;
use backend\models\MessageMassForm;
use Yii;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Chat;
use common\models\Photo;
use common\models\ChatTemplate;
use yii\data\Pagination;
use common\models\Options;
use common\models\UserIpLogAdmin;
use common\models\Sender;
use yii\web\UploadedFile;
/**
 * Site controller
 */
class MessagesController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['unread-messages'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($event)
    {
        if(Options::getOptionValueByKey('logout_admin')) {
            Yii::$app->user->logout();
            return $this->redirect('login');
        }
        ChatTemplate::checkSenders();
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
        $accounts = User::find()->where(['status' => [User::STATUS_MESSAGE_SENDER, User::STATUS_MESSAGE_SENDER_RECIVER]])->all();
        return $this->render('index', [
            'users' => $accounts,
        ]);
    }

    public function actionChats($id)
    {
        if(!$user = User::findIdentity($id)) {
            return $this->redirect('/messages');
        }

        if (!$chats = Chat::getUserChats($id)) {
            $chats = array();
        }
        $users = User::getUsersList();
        return $this->render('chats', [
            'sender_id' => $id,
            'chats' => $chats,
            'users' => $users,
        ]);
    }
    

    public function actionMassMessages()
    {
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $newUser = new MessageMassForm();
            $newUser->load($request->post());
            $newUser->user_id = Yii::$app->user->id;
            $newUser->sendMessageToEveryone();
            
        }

        $accounts = Sender::find()->all();
        $massForm = new MessageMassForm();
        return $this->render('mass-messages', [
            'accounts' => $accounts,
            'massForm' => $massForm,
            'seo' => ['title' => 'Массовые сообщения'],
        ]);
    }

    public function actionHistory()
    {
        $query = MessageMassForm::find()
            ->with('user')
            ->orderBy('date_send DESC');

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 10]);
        $pages->pageSizeParam = false;
       
        
        $messages =  $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('history', [
            'messages' => $messages,
            'pages' => $pages,
            'seo' => ['title' => 'История сообщений'],
        ]);
    }

    public function actionAccounts()
    {
        $accounts = Sender::find()->all();
        return $this->render('accounts', [
            'accounts' => $accounts,
            //'model' => $model,
            'seo' => ['title' => 'Аккаунты рассылок'],
        ]);
    }

    public function actionTemplates()
    {
        $templates = ChatTemplate::find()->with('sender')->all();
        
        return $this->render('templates', [
            'models' => $templates,
            'seo' => ['title' => 'Шаблоны автоматичских сообщений'],
        ]);
    }
    
    public function actionEditTemplate($id)
    {
        $template = ChatTemplate::find()->where(['id' => $id])->with('sender')->one();
        if (Yii::$app->request->isPost) {
            $request = Yii::$app->request;
            $template->load($request->post());
            $template->update();
            Yii::$app->session->setFlash('success', 'Шаблон был успешно изменен');
        }

        $accounts = Sender::find()->all();
        
        return $this->render('edit-template', [
            'model' => $template,
            'accounts' => $accounts,
            'seo' => ['title' => 'Шаблоны автоматичских сообщений'],
        ]);
    }

    public function actionCreateAccount()
    {
        $model = new Sender();

        if (Yii::$app->request->isPost AND $model->load(Yii::$app->request->post())) {
            $model->avatar_file = UploadedFile::getInstance($model, 'avatar_file');
            if($model->saveModel()) {
                Yii::$app->getSession()->setFlash('success', 'Новый отправитель успешно создан');
                return $this->redirect('accounts');
            }

        }

        return $this->render('create-account', [
            'model' => $model,
            'seo' => ['title' => 'Добавить аккаунт'],
        ]);
    }


    public function actionEditAccount($id)
    {
        if (!$model = Sender::findOne($id)) {
            return $this->redirect('accounts');
        }
        if (Yii::$app->request->isPost AND $model->load(Yii::$app->request->post())) {
            $model->avatar_file = UploadedFile::getInstance($model, 'avatar_file');
            if($model->saveModel()) {
                Yii::$app->getSession()->setFlash('success', 'Отправитель успешно изменен');
                return $this->redirect('/messages/accounts');
            }

        }

        return $this->render('create-account', [
            'model' => $model,
            'seo' => ['title' => 'Настройки аккаунта'],
        ]);
    }

   
    public function actionUnreadMessages()
    {
        UserIpLogAdmin::setLog(Yii::$app->user->id,Yii::$app->getRequest()->getUserIP(), Yii::$app->session->getId());
        if (Yii::$app->user->can('admin') AND Yii::$app->request->isAjax) {
            return User::find()->where(['status' => [User::STATUS_MESSAGE_SENDER, User::STATUS_MESSAGE_SENDER_RECIVER]])->sum('unread_messages');
        }
    }
    
    public function actionFindUsersAll()
    {

        if (Yii::$app->request->isAjax) {
            $request = Yii::$app->request;
            $search = $request->post('search');
            $data['status'] = 'Ok';
            if (!$data['users'] = User::findUsersForMessages($search)) {
                $data['status'] = 'Empty';
            }
            return json_encode($data);
        }
    }

}
