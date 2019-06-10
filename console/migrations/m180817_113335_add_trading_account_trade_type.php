<?php

use yii\db\Migration;

/**
 * Class m180817_113335_add_trading_account_trade_type
 */
class m180817_113335_add_trading_account_trade_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'trading_strategy', $this->integer(3)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'trading_strategy');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180817_113335_add_trading_account_trade_type cannot be reverted.\n";

        return false;
    }
    */
}
