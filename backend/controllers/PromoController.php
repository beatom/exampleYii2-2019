<?php
namespace backend\controllers;

use backend\models\PromoBannerImageForm;
use common\models\BalanceBonusLog;
use common\models\BonusDebt;
use common\models\Country;
use common\models\Overdraft;
use common\models\PartnerBaluLog;
use common\models\promo\PromoBanner;
use common\models\promo\PromoBannerImage;
use common\models\trade\Investment;
use common\models\trade\TradingAccount;
use common\models\User;

use common\models\UserIpLog;

use common\service\Servis;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;
use yii\data\Pagination;

/**
 * Site controller
 */
class PromoController extends Controller
{
    public $blocked_resolutions = ['728x90', '240x400'];
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
        $query = PromoBanner::find();
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

    public function actionAdd()
    {
        $model = new PromoBanner();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Баннер успешно создан');
                if (!$this->scan_images($model->id, $model->folder)) {
                    Yii::$app->getSession()->setFlash('success', 'Файлы изображений успешно просканированы');
                }
                return $this->redirect(['promo/edit/' . $model->id]);
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'title' => 'Добавление баннера'
        ]);
    }


    public function actionAdd_html($id)
    {
        if (!$model = PromoBanner::findIdentity($id)) {
            return $this->redirect(['promo/index']);
        }
        $html_image = new PromoBannerImageForm();

        if ($html_image->load(Yii::$app->request->post())) {
            $html_image->id;

            if ($html_image->saveChange()) {
                Yii::$app->getSession()->setFlash('success', 'Html баннер успешно создан');
                return $this->redirect(['promo/size/' . $id . '/' . $html_image->sizex . 'x' . $html_image->sizey]);
            }
        }

        $sizes = PromoBanner::getSizes($id, false);
        return $this->render('add_html', [
            'model' => $model,
            'sizes' => $sizes,
            'html_image' => $html_image,
            'title' => 'Добавление html баннера'
        ]);
    }

    public function actionScan_image($id)
    {
        if (!$model = PromoBanner::findIdentity($id)) {
            return $this->redirect(['promo/index']);
        }
        if (!$this->scan_images($id, $model->folder)) {
            Yii::$app->getSession()->setFlash('success', 'Файлы изображений успешно просканированы');
        }

        return $this->redirect(['promo/edit/' . $id]);
    }

    public function actionResetMain($id)
    {
        if (!$model = PromoBanner::findIdentity($id)) {
            return $this->redirect(['promo/index']);
        }
        if (!$this->reset_main($id)) {
            Yii::$app->getSession()->setFlash('success', 'Отображаемы езображения перезаданы');
        }
        return $this->redirect(['promo/edit/' . $id]);
    }

    public function scan_images($id, $front_end_path)
    {

        $path_to_promo = dirname(dirname(__DIR__)) . '/frontend/web' . $front_end_path;
        $scan = scandir($path_to_promo);
        if (!$scan) {
            return false;
        }
        foreach ($scan as $dir) {
            if ($dir === '.' OR $dir === '..') {
                continue;
            }
            $inner_path = $path_to_promo . '/' . $dir;
            if (!$files = scandir($inner_path)) {
                continue;
            }

            foreach ($files as $f) {
                $file_path = $inner_path . '/' . $f;
                if (!is_file($file_path)) {
                    continue;
                }
                if (!$image_info = getimagesize($file_path)) {
                    $ext = pathinfo($file_path, PATHINFO_EXTENSION);
                    if ($ext == 'html' OR $ext == 'htm') {
                        $dir_parts = explode("-", $dir);

                        if($dir == $dir_parts[0]){
                            $dir_parts =  explode(".", $dir);
                        }
                        if (count($dir_parts) == 2) {

                            if (!$image = PromoBannerImage::find()
                                ->where(['promo_banner_id' => $id, 'size' => $dir_parts[0] . 'x' . $dir_parts[1], 'link' => $front_end_path . '/' . $dir . '/' . $f, 'type' => $ext])->one()
                            ) {
                                $image = new PromoBannerImage();
                            }

                            $image->size = $dir_parts[0] . 'x' . $dir_parts[1];
                            $image->html_size = 'width="' . ($dir_parts[0] + 4) . '" height="' . ($dir_parts[1]+4) . '"';

                            $image->promo_banner_id = $id;
                            $image->type = $ext;
                            $image->link = $front_end_path . '/' . $dir . '/' . $f;
                            if(in_array(pathinfo($file_path,  PATHINFO_FILENAME).'.zip', $files)) {
                                $image->archive_link = $front_end_path . '/' . $dir . '/'. pathinfo($file_path,  PATHINFO_FILENAME).'.zip';
                            }
                            $image->save();
                        }
                    }
                    continue;
                }
                if( strripos ( $f, '_atlas_')) {
                    continue;
                }
                $img_extantion = substr(image_type_to_extension($image_info[2]), 1);
                if (!$image = PromoBannerImage::find()
                    ->where(['promo_banner_id' => $id, 'size' => $image_info[0] . 'x' . $image_info[1], 'link' => $front_end_path . '/' . $dir . '/' . $f, 'type' => $img_extantion])->one()
                ) {
                    $image = new PromoBannerImage();
                }
                $image->html_size = 'width="' . $image_info[0] . '" height="' . $image_info[1] . '"';
                $image->promo_banner_id = $id;
                $image->type = $img_extantion;
                $image->size = $image_info[0] . 'x' . $image_info[1];
                $image->link = $front_end_path . '/' . $dir . '/' . $f;
                $image->save();
            }
        }
        $this->reset_images($id);
        return true;
    }

    public function reset_images($id)
    {

        $images = PromoBannerImage::find()->where(['promo_banner_id' => $id])->groupBy('size, type')->orderBy('id')->all();
        $done_array = [];
        foreach ($images as $image) {
            if (!in_array($image->size . $image->type, $done_array)) {
                $image->is_main = true;
                $image->save();
                $done_array[] = $image->size . $image->type;
                continue;
            }
        }
        return true;
    }


    public function actionEdit($id)
    {
        if (!$model = PromoBanner::findIdentity($id)) {
            return $this->redirect(['promo/index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'Баннер успешно обновлен');
            }
        }

        $sizes = PromoBanner::getSizes($id, false);
        $types = PromoBanner::getTypes($id, false);
        return $this->render('edit', [
            'model' => $model,
            'sizes' => $sizes,
            'types' => $types,
            'title' => 'Баннер ' . $model->name,
        ]);
    }


}
