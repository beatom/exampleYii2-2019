<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        
        $manager = $auth->createRole('manager');
        $auth->add($manager);
        
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $manager);
    }

    public function actionTest()
    {
        $user = new User();
        $user = $user->createUser('admin', 'admin@test.ru', '111111aa');
        $user->status = 1;
        $user->save();

        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('admin');
        $auth->assign($admin, 1);
    }

    public function actionAdmin($id)
    {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('admin');
        $auth->assign($admin, $id);
    }

    public function actionModerators() {
        $auth = Yii::$app->authManager;
        $admin = $auth->getRole('admin');
        $moderator = $auth->createRole('moderator');
        $auth->add($moderator);
        $auth->addChild($admin, $moderator);
    }
}
