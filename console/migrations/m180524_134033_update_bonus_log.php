<?php

use yii\db\Migration;

/**
 * Class m180524_134033_update_bonus_log
 */
class m180524_134033_update_bonus_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('balance_bonus_log', 'summ', $this->double()->null()->defaultValue(0));
        $this->alterColumn('balance_bonus_log', 'summ_now', $this->double()->null()->defaultValue(0));
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
        echo "m180524_134033_update_bonus_log cannot be reverted.\n";

        return false;
    }
    */
}
