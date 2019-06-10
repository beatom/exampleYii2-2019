<?php
use common\models\User;

?>
<div class="password-reset">
    <p>Привет Админ,</p>

    <p>Пользователь <?= $user->username ?> получил статус <?= User::$partner_staus[$user->status_in_partner] ?></p>

</div>
