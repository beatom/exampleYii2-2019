<?php

use yii\db\Migration;

/**
 * Class m180616_075756_update_period_log
 */
class m180616_075756_update_period_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
           // 'ALTER TABLE `traiding_period_log` CHANGE `report` `report` LONGTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;'
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180616_075756_update_period_log cannot be reverted.\n";

        return false;
    }
    */
}
