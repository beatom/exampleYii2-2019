<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class CabinetAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/cabinet.css',
        'css/__custom.css',
        'css/_cabinet.css',
    ];
    
    public $js = [
        'js/isotope.pkgd.js',
        'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js',
        'js/cabinet-libs.min.js',
        'js/bodyScrollLock.js',
        'js/main.min.js',
        'js/custom.js',
        'js/custom-cabinet.js'
    ];
    
    public $depends = [
       'yii\web\YiiAsset',
       'yii\bootstrap\BootstrapAsset',
    ];
}
