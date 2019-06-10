<?php

$menuItems=[];
//$menuItems[] = ['label' => 'Добавить html баннер', 'url' => '/promo/add_html/'. $banner_id];
$menuItems[] = ['label' => 'Сканировать папку', 'url' => '/promo/scan_image/'. $banner_id];
foreach ($items as $item) {
    $menuItems[] = ['label' => $item, 'url' => '/promo/size/'. $banner_id.'/'.$item];
}

?>
<div class="vertical-menu">
    <?php
    foreach ($menuItems as $item){
        $class = '';
        if($_SERVER['REDIRECT_URL'] == $item['url']){
            $class = ' class="active"';
        }
        echo '<a href="'.$item['url'].'"'.$class.'>'.$item['label'].'</a>';
    } ?>

</div>