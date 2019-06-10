<?php

namespace frontend\controllers;

use common\models\ManagerCard;
use common\models\User;
use Yii;
use yii\rest\Controller;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;
use common\models\AmoUserPipelines;
/**
 * Api controller
 */
class ApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats'] = ['application/json' => Response::FORMAT_JSON];

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Credentials' => true,
            ],

        ];


//        unset($behaviors['authenticator']);
//        $behaviors['authenticator'] = [
//            'class' =>  HttpBearerAuth::class,
//        ];

//        $behaviors['access'] = [
//            'class' => AccessControl::class,
//            'rules' => [
//                [
//                    'allow' => true,
//                    'roles' => ['@'],
//                ],
//            ],
//        ];
//
        return $behaviors;
    }

    public function actionIndex()
    {
        $a = [
            'contacts' => [
                'update' => [
                    [
                        'id' => '21586271',
                        'name' => 'testAmoApi9',
                        'responsible_user_id' => '2452678',
                        'group_id' => '178348',
                        'date_create' => '1538145956',
                        'last_modified' => '1538145977',
                        'created_user_id' => '2451640',
                        'modified_user_id' => '2451640',
                        'old_responsible_user_id' => '2452678',
                        'type' => 'contact',
                    ],
                ],

            ],
            'account' => [
                'subdomain' => 'invest24sd',
            ]
        ];
        $b = [
            'request' => [
                'leads' => [
                    'responsible' => [
                        [
                            'id' => '13146661',
                            'name' => 'Потенциальный клиент',
                            'status_id' => '24372226',
                            'old_status_id' => '24370219',
                            'price' => '0',
                            'responsible_user_id' => '2451640',
                            'last_modified' => '1552316151',
                            'modified_user_id' => '2451640',
                            'created_user_id' => '2451640',
                            'date_create' => '1550675703',
                            'pipeline_id' => '1601338',
                            'account_id' => '19987162',
                            'old_responsible_user_id' => '3288886',
                        ],
                    ],
                ],
                'account' => [
                    'subdomain' => 'invest24sd',
                    'id' => '19987162',
                    '_links' => [
                        'self' => 'https://invest24sd.amocrm.ru',
                    ],
                ],
            ]
        ];

        var_dump(json_encode($a));
        return ["index"];
    }

    public function actionAmoContactResponsibleChange()
    {
        echo 'Ok';
        \Yii::info(['name' => 'actionAmoContactResponsibleChange',
            'request' => Yii::$app->request->post(),
        ], 'api');
        $post = Yii::$app->request->post();
//        if (!isset($post['contacts']['update'][0]['id']) OR !isset($post['contacts']['update'][0]['responsible_user_id'])) {
//            return 'Fail';
//        }
        if(isset($post['contacts'])) {
            if ($manager = ManagerCard::find()->where(['amo_user_id' => $post['contacts']['update'][0]['responsible_user_id']])->one()) {
                User::updateAll(['manager_card_id' => $manager->id], ['amo_contact_id' => $post['contacts']['update'][0]['id']]);
            }
        }
        if(isset($post['leads'])) {
            foreach ($post['leads']['responsible'] as $lead) {
                if($lead['pipeline_id'] == 1601320  //если сделка в воронке Synergy
                    AND $user_info = AmoUserPipelines::find()->where(['synergy_1' => $lead['id']])->with('user')->one()
                    AND $manager = ManagerCard::find()->where(['amo_user_id' => $lead['responsible_user_id']])->one()) {
                    $user = $user_info->user;
                    $user->manager_card_id = $manager->id;
                    $user->save();
                }
            }

            if ($manager = ManagerCard::find()->where(['amo_user_id' => $post['contacts']['update'][0]['responsible_user_id']])->one()) {
                $query = User::updateAll(['manager_card_id' => $manager->id], ['amo_contact_id' => $post['contacts']['update'][0]['id']]);
            }
        }
    }

    public function actionAmoLeadResponsibleChange()
    {
        echo 'Ok';
        \Yii::info(['name' => 'actionAmoLeadResponsibleChange',
            'request' => Yii::$app->request->post(),
        ], 'api');
        $post = Yii::$app->request->post();
//        if (!isset($post['contacts']['update'][0]['id']) OR !isset($post['contacts']['update'][0]['responsible_user_id'])) {
//            return 'Fail';
//        }
//        if (!$manager = ManagerCard::find()->where(['amo_user_id' => $post['contacts']['update'][0]['responsible_user_id']])->one()) {
//            return 'Fail';
//        }
//        $query = User::updateAll(['manager_card_id' => $manager->id], ['amo_contact_id' => $post['contacts']['update'][0]['id']]);
//        return $query;
    }

    public function actionAmoWidgetGetUsersLevels()
    {
        \Yii::info(['name' => 'actionAmoWidgetGetUsersLevels',
            'request' => Yii::$app->request->post(),
        ], 'api');
        $post = Yii::$app->request->post();
        $models = User::find()->where(['amo_contact_id' => $post['users'], 'amo_tag_level' => [1, 2, 4]])->all();
        $result = [];
        foreach ($models as $model) {
            $result[$model->amo_contact_id] = $model->amo_tag_level;
        }
        return json_encode($result);
    }

}
