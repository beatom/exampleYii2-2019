<?php

use yii\db\Migration;

/**
 * Class m180505_120516_update_investment_log_table
 */
class m180505_120516_update_investment_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('traiding_investments_log', 'trading_account_id', $this->integer(11)->null()->defaultValue(null));
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
        echo "m180505_120516_update_investment_log_table cannot be reverted.\n";

        return false;
    }
    */
}
