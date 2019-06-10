<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/first-slide.css',
        'css/main.css',
        'css/__custom.css',
        'css/_main.css',
    ];
    public $js = [
        'js/three.min.js',
        'js/WebGL.js',
        'js/libs.min.js',
        'js/bodyScrollLock.js',
        'js/main.min.js',
        'js/jquery.cookie.js',
        'js/custom.js',
        'js/custom-main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
