<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/registration.css',
        'css/__custom.css',
        'css/_registration.css',
    ];
    public $js = [
        'js/libs.min.js',
        'js/main.min.js',
        'js/custom.js',
        'js/custom-login.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
