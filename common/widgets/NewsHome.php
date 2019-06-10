<?php

namespace common\widgets;

use yii\base\Widget;
use common\models\Shares;
use common\models\News;

class NewsHome extends Widget
{
    public $news;

    public function init(){

        parent::init();
        $this->news = News::getActiveNews( 3 );
    }

    public function run(){

        return $this->render('news-home', [
            'news' => $this->news
        ]);
    }
}