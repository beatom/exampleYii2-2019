<?php
namespace backend\controllers;

use backend\models\AddWebinarForm;
use common\models\Webinar;
use common\models\WebinarArchive;
use yii\helpers\Url;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;
use backend\models\AddWebinarArchiveForm;
/**
 * Site controller
 */
class WebinarController extends Controller
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
                        'actions' => ['logout', 'index','add', 'edit', 'archive', 'list', 'make'],
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
        $query = Webinar::find()->orderBy('date_end DESC');
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

        $model = new AddWebinarForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->addNews()){
                Yii::$app->getSession()->setFlash('success', 'Вебинар успешно добавлен');
                return $this->redirect(['index']);
            }
        }

        return $this->render('edit',[
            'model'=> $model,
            'seo'=>['title'=>'Добавить вебинар']
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

        $news = Webinar::findIdentity($id);

        if(!$news) {
            $this->redirect(Url::to('/webinar/index'));
        }

        $model = new AddWebinarForm();
        $model->setData($news);

        if ($model->load(Yii::$app->request->post())) {
            if($model->addNews( false, $news )){
                Yii::$app->getSession()->setFlash('success', 'Вебинар успешно изменен');
                return $this->redirect(['index']);
            }
        }

        return $this->render('edit',[
            'model'=> $model,
            'seo'=>['title'=>'Редактировать вебинар',
                'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }

    public function actionArchive( $id )
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $news = WebinarArchive::findIdentity($id);

        if(!$news) {
            $this->redirect(Url::to('/webinar/list'));
        }

        $model = new AddWebinarArchiveForm();
        $model->setData($news);

        if ($model->load(Yii::$app->request->post())) {
            if($model->addNews( false, $news )){
                Yii::$app->getSession()->setFlash('success', 'Архивная запись успешно изменена');
                return $this->redirect(['list']);
            }
        }

        return $this->render('archive',[
            'model'=> $model,
            'seo'=>['title'=>'Редактировать вебинар',
                'frontend_domen' => Yii::$app->params['frontendDomen'],
            ]
        ]);
    }


    public function actionList()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        // выполняем запрос
        $query = WebinarArchive::find()->orderBy('date_add DESC');
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
        return $this->render('list', [
            'models' => $models,
            'pages' => $pages,
        ]);
    }


    public function actionMake()
    {
        $user = Yii::$app->user->identity;
        $user->setForbidden();

        $model = new AddWebinarArchiveForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->addNews()){
                Yii::$app->getSession()->setFlash('success', 'Архивная запись успешно добавлена');
                return $this->redirect(['list']);
            }
        }

        return $this->render('archive',[
            'model'=> $model,
            'seo'=>['title'=>'Добавить вебинар']
        ]);
    }

}
