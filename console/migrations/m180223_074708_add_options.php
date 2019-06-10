<?php

use yii\db\Migration;

/**
 * Class m180223_074708_add_options
 */
class m180223_074708_add_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'facebook',
                'value' => '#facebook',
                'description' => 'link to accaunt',
            ],
            [
                'key' => 'vk',
                'value' => '#vk',
                'description' => 'link to accaunt',
            ],
            [
                'key' => 'twitter',
                'value' => '#tw',
                'description' => 'link to accaunt',
            ],
            [
                'key' => 'instagram',
                'value' => '#instagram',
                'description' => 'link to accaunt',
            ],
            [
                'key' => 'youtube',
                'value' => '#youtube',
                'description' => 'link to accaunt',
            ],
            [
                'key' => 'WIDEO_ON_HOME',
                'value' => '',
                'description' => 'Блок с видео на главной',
            ],
            [
                'key' => 'HOME_SIMPLE_AS',
                'value' => '',
                'description' => 'Блок "ЭТО ПРОСТО КАК" на главной',
            ],
            [
                'key' => 'PLAN_HOME',
                'value' => '',
                'description' => 'Блок с планами',
            ],
            [
                'key' => 'WHY_invest',
                'value' => '',
                'description' => 'Блок "Почему invest"',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DELETE FROM `options` WHERE `key` = "WHY_invest" OR `key`="youtube" OR `key`="twitter" OR `key`="instagram" OR `key`="facebook" OR `key`="vk" OR `key`="WIDEO_ON_HOME" OR `key`="HOME_SIMPLE_AS" OR `key`="PLAN_HOME" ');

        return true;
    }
}
