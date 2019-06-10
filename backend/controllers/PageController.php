<?php
namespace backend\controllers;

use backend\models\AddNewsForm;
use backend\models\AddPageForm;
use common\models\News;
use common\models\Page;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;

/**
 * Site controller
 */
class PageController extends Controller
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
                        'actions' => ['image-upload'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index','add', 'edit'],
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

        //готовим запрос
        $my_query = 'date_add DESC';

        if(isset($_GET['sort_id'])){
            $my_query = 'id ' .$_GET['sort_id'];
        }
        else if(isset($_GET['sort_date'])){
            $my_query = 'date_add '. $_GET['sort_date'];
        }


        // выполняем запрос
        $query = Page::find()->orderBy( $my_query );

        if(!empty($_GET['search'])){
            $query->where(['like', 'title', $_GET['search']]);
        }

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
    public function actionAdd()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $model = new AddPageForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->add()){
                $this->redirect(['index']);
            }
        }

        return $this->render('add',[
            'model'=> $model,
            'seo'=>['title'=>'Добавить страницу']
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

        $page = Page::findIdentity($id);

        if(!$page) {
            $this->redirect(Url::to('/page/index'));
        }

        $model = new AddPageForm();
        $model->setData($page);

        if ($model->load(Yii::$app->request->post())) {
            if($model->add( false, $page )){
                $this->redirect(['index']);
            }
        }

        return $this->render('add',[
            'model'=> $model,
            'seo'=>['title'=>'Редактировать страницу',
                'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }

}
