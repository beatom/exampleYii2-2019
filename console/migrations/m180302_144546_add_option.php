<?php

use yii\db\Migration;

/**
 * Class m180302_144546_add_option
 */
class m180302_144546_add_option extends Migration
{

    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'trede_static_page_top',
                'value' => '',
                'description' => 'Страница торговать топ',
            ],
            [
                'key' => 'trede_static_page_slide',
                'value' => '',
                'description' => 'Страница торговать слайдер',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DELETE FROM `options` WHERE `key` = "trede_static_page_slide" OR `key`="trede_static_page_top"');

        return true;
    }
}
