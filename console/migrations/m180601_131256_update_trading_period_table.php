<?php

use yii\db\Migration;

/**
 * Class m180601_131256_update_trading_period_table
 */
class m180601_131256_update_trading_period_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_period_log', 'close_deals', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('traiding_period_log', 'close_deals');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180601_131256_update_trading_period_table cannot be reverted.\n";

        return false;
    }
    */
}
