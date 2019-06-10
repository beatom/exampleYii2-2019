<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Привет <?= $user->username ?>,

Следуйте приведенной ниже ссылке, чтобы сбросить пароль.:

<?= $resetLink ?>
