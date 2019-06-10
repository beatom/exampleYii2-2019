<?php

use yii\db\Migration;

/**
 * Class m180521_101130_trading_period_lo_update
 */
class m180521_101130_trading_period_lo_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_period_log', 'report', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('traiding_period_log', 'report');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180521_101130_trading_period_lo_update cannot be reverted.\n";

        return false;
    }
    */
}
