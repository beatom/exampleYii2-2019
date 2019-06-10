<?php

$menuItems=[];
$menuItems[] = ['label' => 'События', 'url' => '/events/list'];
$menuItems[] = ['label' => 'Статистика по дням', 'url' => '/events/index'];
$menuItems[] = ['label' => 'История', 'url' => '/events/history'];
//$menuItems[] = ['label' => 'Редактировать', 'url' => '/trade/edit-account/'];
?>
<div class="topnav">
    <?php
    foreach ($menuItems as $item){
        $class = '';
        if($_SERVER['REDIRECT_URL'] == $item['url']){
            $class = ' class="active"';
        }
        echo '<a href="'.$item['url'].'"'.$class.'>'.$item['label'].'</a>';
    } ?>

</div>

<style>
    /* Add a black background color to the top navigation */
    .topnav {
       // background-color: #333;
        overflow: hidden;
        margin-bottom: 30px;
    }

    /* Style the links inside the navigation bar */
    .topnav a {
        float: left;
        color: black;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        font-size: 17px;
        border: 1px solid  #4CAF50;
    }

    /* Change the color of links on hover */
    .topnav a:hover {
        background-color: #ddd;
        color: black;
    }

    /* Add a color to the active/current link */
    .topnav a.active {
        background-color: #4CAF50;
        color: white;
    }
</style>