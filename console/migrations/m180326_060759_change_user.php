<?php

use yii\db\Migration;

/**
 * Class m180326_060759_change_user
 */
class m180326_060759_change_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'status_in_partner', $this->integer(11)->defaultValue(1)->after('partner_id')->comment('статус в партнерке'));

        $this->addColumn('banner', 'text_button_en', $this->string()->null()->defaultValue(null)->after('text_button'));
        $this->addColumn('banner', 'title_en', $this->string()->null()->defaultValue(null)->after('title'));
        $this->addColumn('banner', 'subtitle_en', $this->string()->null()->defaultValue(null)->after('subtitle'));
        $this->addColumn('banner', 'text_1_en', $this->string()->null()->defaultValue(null)->after('text_1'));
        $this->addColumn('banner', 'text_2_en', $this->string()->null()->defaultValue(null)->after('text_2'));

        $this->addColumn('investments_plan', 'name_en', $this->string()->after('name')->null()->defaultValue(null));

        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'WIDEO_ON_HOME_EN',
                'value' => '',
                'description' => '',
            ],
            [
                'key' => 'HOME_SIMPLE_AS_EN',
                'value' => '',
                'description' => '',
            ],
            [
                'key' => 'WHY_invest_EN',
                'value' => '',
                'description' => '',
            ],
        ]);

        $this->addColumn('shares', 'title_en', $this->string()->null()->defaultValue(null)->after('title'));
        $this->addColumn('shares', 'text_small_en', $this->text()->null()->defaultValue(null)->after('text_small'));
        $this->addColumn('shares', 'text_big_en', $this->text()->null()->defaultValue(null)->after('text_big'));

        $this->addColumn('news', 'title_en', $this->string()->null()->defaultValue(null)->after('title'));
        $this->addColumn('news', 'text_small_en', $this->text()->null()->defaultValue(null)->after('text_small'));
        $this->addColumn('news', 'text_big_en', $this->text()->null()->defaultValue(null)->after('text_big'));

        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'partnership_fullpage_en',
                'value' => '',
                'description' => '',
            ],
            [
                'key' => 'partnership_hiw_en',
                'value' => '',
                'description' => '',
            ],
        ]);

        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'trede_static_page_slide_en',
                'value' => '',
                'description' => '',
            ],
        ]);

        $this->dropColumn('traiding_plan', 'execution');
        $this->dropColumn('traiding_plan', 'spred');
        $this->addColumn('traiding_plan','spred', $this->float(11)->defaultValue(0));
        $this->dropColumn('traiding_plan', 'commission');
        $this->addColumn('traiding_plan','commission', $this->float(11)->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return true;
    }

}
