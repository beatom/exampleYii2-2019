<?php

namespace common\widgets;

use yii\base\Widget;
use common\models\Banner;
use common\models\Options;

class SliderHome extends Widget
{
    public $slids;
    public $social;

    public function init(){

        parent::init();
        $this->slids = Banner::getActiveBaners( 2 );
//        $this->social = Options::getSocialLink();
    }

    public function run(){

        return $this->render('slider-home', [
            'slids' => $this->slids,
//            'social' => $this->social
        ]);
    }
}