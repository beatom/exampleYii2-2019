<?php
use yii\helpers\Url;

$lang = \Yii::$app->language;
$languages = ['en' => 'En', 'ru' => 'Ru'];
?>
<div class="change__lang__item">
    <ul>
        <?php foreach ($languages as $key => $value) {
            if($key == $lang) {
                echo '<li><a href="/'.$key.'/user/index" class="active">'.$value.' </a></li>';
            } else {
                echo '<li><a href="/'.$key.'/user/index">'.$value.'</a></li>';
            }
        } ?>
    </ul>
</div>