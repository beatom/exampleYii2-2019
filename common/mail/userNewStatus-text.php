<?php
use common\models\User;

?>
Привет Админ,

Пользователь <?= $user->username ?> получил статус <?= User::$partner_staus[$user->status_in_partner] ?>

