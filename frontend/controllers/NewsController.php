<?php
namespace frontend\controllers;

use common\service\Servis;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\News;
use yii\data\Pagination;
use Yii;
use yii\web\Cookie;

/**
 * Site controller
 */
class NewsController extends Controller
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
                'rules' => [
                    [
                        'actions' => ['index', 'single'],
                        'allow' => true,
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
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function beforeAction($event)
    {

//        if (!Yii::$app->request->cookies->has('user_authenticate') OR @!Yii::$app->user->identity->popup_banner_shown) {
//            if (!Yii::$app->user->isGuest) {
//                $user = Yii::$app->user->identity;
//                $user->popup_banner_shown = false;
//                $user->save();
//            }
//            Yii::$app->response->cookies->add(new Cookie([
//                'name' => 'user_authenticate',
//                'value' => date('Y-m-d H:i:s'),
//                'httpOnly' => false,
//                'expire' => time() + 1800,
//            ]));
//        }

        return parent::beforeAction($event);
    }

  
    public function actionIndex()
    {
        $id = false;
        if(isset($_GET['id']) AND News::findIdentity($_GET['id'])) {
            $id = $_GET['id'];
        }
        $page_size = 10;

        // выполняем запрос
        $query = News::find()
	                 ->where('status = 1')
                     ->orderBy('date_add DESC' );
        if(isset($_GET['from']) AND $_GET['from'] != '') {
            $query->andWhere('`from` LIKE "' . $_GET['from'] . '" OR from_en LIKE "' . $_GET['from'] .'"' );
        }
        if(isset($_GET['cat']) AND $_GET['cat'] != '') {
            $query->andWhere('cat LIKE "' . $_GET['cat'] . '" OR cat_en LIKE "' . $_GET['cat'] .'"' );
        }
        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => $page_size]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;
        $p = false;
        if($id) {
            $p = News::find()->where(['status' => 1])->andWhere('id < '. $id)->orderBy('date_add DESC')->count();
        }

        $offset = $p ? ($p-$p%$page_size)/$page_size : $pages->offset;
        $models = $query->offset($offset)
            ->limit($pages->limit)
            ->all();

        // Передаем данные в представление
        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
            'selected_id' => $id,
            'seo' => ['title'=>Yii::t('app','Новости'), 'type'=>'news']
        ]);
    }

    public function actionSingle( $id, $synonym ){

        $news = News::findIdentity($id);
        $news->markAsRead();
        $news = Servis::getInstance()->translete($news);
        return $this->render('single', [
            'model' => $news
        ]);
    }
}