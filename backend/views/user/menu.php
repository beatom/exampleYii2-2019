<?php

$menuItems=[];
$menuItems[] = ['label' => 'Информация', 'url' => '/user/'. $user_id];
$menuItems[] = ['label' => 'Редактировать', 'url' => '/user/edit/'. $user_id];
$menuItems[] = ['label' => 'Баланс', 'url' => '/user/'. $user_id.'/balance'];
$menuItems[] = ['label' => 'Сообщения', 'url' => '/user/messages/'. $user_id];
$menuItems[] = ['label' => 'Цели', 'url' => '/user/objectives/'. $user_id];
$menuItems[] = ['label' => 'Партнерка', 'url' => '/user/'. $user_id.'/partnerka'];
//$menuItems[] = ['label' => 'Торговые счета', 'url' => '/user/accounts/'. $user_id];
//$menuItems[] = ['label' => 'Инвестиции', 'url' => '/user/investments/'. $user_id];
$menuItems[] = ['label' => 'Верификация', 'url' => '/user/verification/'. $user_id];
$menuItems[] = ['label' => 'Лог ip', 'url' => '/user/iplog/'. $user_id];

?>
<div class="vertical-menu">
    <?php
    foreach ($menuItems as $item){
        $class = '';
        if($_SERVER['REQUEST_URI'] == $item['url']){
            $class = ' class="active"';
        }
        echo '<a href="'.$item['url'].'"'.$class.'>'.$item['label'].'</a>';
    } ?>

</div>
