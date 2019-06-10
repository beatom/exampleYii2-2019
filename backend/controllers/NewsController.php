<?php
namespace backend\controllers;

use backend\models\AddNewsForm;
use common\models\News;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;

/**
 * Site controller
 */
class NewsController extends Controller
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
                        'actions' => ['logout', 'index','add-news', 'edit'],
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
                'url' => '//'.Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend').'/web/upload/pages/' // Or absolute path to directory where files are stored.
            ],
            'images-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => '//'.Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend').'/web/upload/pages/', // Or absolute path to directory where files are stored.
                'type' => '0',
            ],
            'files-get' => [
                'class' => 'vova07\imperavi\actions\GetAction',
                'url' => '//'.Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend').'/web/upload/pages/', // Or absolute path to directory where files are stored.
                'type' => '1',//GetAction::TYPE_FILES,
            ],
            'file-upload' => [
                'class' => 'vova07\imperavi\actions\UploadAction',
                'url' => '//'.Yii::$app->params['frontendDomen'] . '/upload/pages/', // Directory URL address, where files are stored.
                'path' => Yii::getAlias('@frontend').'/web/upload/pages/' // Or absolute path to directory where files are stored.
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
        $user = Yii::$app->user->identity;
        $user->setForbidden();

	    $sort = (!empty($_GET['fild'])) ? $_GET['fild'] : 'date_add';
	    $sort .= (!empty($_GET['order_by'])) ? ' ASC' : ' DESC';

        // выполняем запрос
        $query = News::find()->orderBy( $sort );
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
        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    /**
     * Displays add news.
     *
     * @return string
     */
    public function actionAddNews()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $model = new AddNewsForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->addNews()){
                $this->redirect(['index']);
            }
        }

        return $this->render('add-news',[
            'model'=> $model,
            'seo'=>['title'=>'Добавить новость']
        ]);
    }

    /**
     * Displays add news.
     *
     * @return string
     */
    public function actionEdit( $id )
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $news = News::findIdentity($id);

        if(!$news) {
            $this->redirect(Url::to('/news/index'));
        }

        $model = new AddNewsForm();
        $model->setData($news);

        if ($model->load(Yii::$app->request->post())) {
            if($model->addNews( false, $news )){
                $this->redirect(['index']);
            }
        }

        return $this->render('add-news',[
            'model'=> $model,
            'seo'=>['title'=>'Редактировать новость',
                    'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }

}
