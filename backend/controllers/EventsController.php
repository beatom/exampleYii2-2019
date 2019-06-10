<?php
namespace backend\controllers;


use common\models\DaysLog;

use common\models\Events;
use common\models\promo\PromoBanner;

use common\models\Sender;
use common\models\trade\TradingAccount;

use common\service\Servis;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;
use backend\models\EventsForm;

/**
 * Site controller
 */
class EventsController extends Controller
{
    public $blocked_resolutions = ['728x90', '240x400'];

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
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actionIndex()
    {

        $query = DaysLog::find()->orderBy('date_add DESC');

        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        if (empty($models)) {
            $models[] = DaysLog::getLog();
        }


        return $this->render('index', [
            'models' => $models,
            'pages' => $pages,
           
            'seo' => ['title' => 'Статистика по дням']
        ]);
    }

    public function actionList()
    {
        $h = date('H');
        
        if ($h < 10) {
            $date = date('Y-m-d', strtotime('yesterday'));
        } else {
            $date = date('Y-m-d');
        }

        $days_log = DaysLog::getLog($date);
        $query = Events::find()->orderBy('date_add DESC')->where(['days_log_id' => $days_log->id]);
        
        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        $model = new EventsForm();
        $model->getData();

        if (Yii::$app->request->isPost AND $model->load(Yii::$app->request->post())) {
            if ($model->saveForm()) {
                Yii::$app->getSession()->setFlash('success', 'Сообщение успешно изменено');
                
            }
        }
        
        return $this->render('list', [
            'days_log' => $days_log,
            'models' => $models,
            'pages' => $pages,
            'model_form' => $model,
            'accounts' => Sender::find()->all(),
            'seo' => ['title' => 'События за ' . $days_log->date_add]
        ]);
    }

    public function actionHistory()
    {

        $query = Events::find()->orderBy('date_add DESC');
        if (!empty($_GET['date_add'])) {
            if($day_log = DaysLog::find()->where(['date_add' => date('Y-m-d', strtotime($_GET['date_add']))])->one()) {
                $query->where(['days_log_id' => $day_log->id]);
            }
        }
        // делаем копию выборки
        $countQuery = clone $query;
        // подключаем класс Pagination, выводим по 10 пунктов на страницу
        $pages = new Pagination(['totalCount' => $countQuery->count(), 'pageSize' => 20]);
        // приводим параметры в ссылке к ЧПУ
        $pages->pageSizeParam = false;

        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('history', [
            'models' => $models,
            'pages' => $pages,
            'seo' => ['title' => 'История событий']
        ]);
    }

    public function actionAdd($id = false)
    {
        $model = new Events();
        $days_log = false;
        if ($id) {
            $days_log = DaysLog::findIdentity($id);
            $model->days_log_id = $id;
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!$days_log) {
                $days_log = DaysLog::getLog($model->date_add);
                $model->days_log_id = $days_log->id;
            }

            if ($model->saveEvent()) {
                Yii::$app->getSession()->setFlash('success', 'Событие успешно добавлено');
                return $this->redirect(['/events/list/' . $days_log->id]);
            }
        }

        return $this->render('single', [
            'model' => $model,
            'days_log' => $days_log,
            'seo' => ['title' => 'Добавление события ']
        ]);
    }

    public function actionEdit($id)
    {
        $model = Events::findIdentity($id);
        if (!$model) {
            return $this->redirect(['/events/list']);
        }
        $days_log = $model->days_log;

        if ($days_log->comment) {
            Yii::$app->getSession()->setFlash('error', 'Статистика за день уже посчитана и событие не может быть изменено');
            return $this->redirect(['/events/list/' . $days_log->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveEvent()) {
                Yii::$app->getSession()->setFlash('success', 'Событие успешно изменено');
                return $this->redirect(['/events/list/' . $days_log->id]);
            }
        }

        return $this->render('single', [
            'model' => $model,
            'days_log' => $days_log,
            'seo' => ['title' => 'Редактирование события ']
        ]);
    }

    public function actionReport($id)
    {
        $model = DaysLog::findIdentity($id);
        if (!$model) {
            return $this->redirect(['/events/list']);
        }

        if ($model->comment) {
            $report = $model->comment;
        } else {
            $model->count();
            $model->refresh();
            $report = $model->getAllComment();
        }

        return $this->render('report', [
            'report' => $report,
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = Events::findIdentity($id);
        if (!$model) {
            return $this->redirect(['/events/list']);
        }
        $days_log = DaysLog::findIdentity($model->days_log_id);
        $model->delete();
        Yii::$app->getSession()->setFlash('success', 'Событие успешно удалено');
        if ($days_log) {
            $days_log->count();
            return $this->redirect(['/events/list/' . $days_log->id]);
        }
        return $this->redirect(['/events/list']);
    }

}
