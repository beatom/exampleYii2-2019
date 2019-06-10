<?php

namespace common\service;

use yii\helpers\Html;

class Nav extends \yii\bootstrap\Nav
{
    public function init()
    {
        parent::init();
        Html::removeCssClass($this->options, 'nav');
    }
}