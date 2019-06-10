<?php

use yii\db\Migration;

/**
 * Class m180417_083057_update_trading_account__table
 */
class m180417_083057_update_trading_account__table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'profit_summ', $this->double()->notNull()->defaultValue(0)->after('profit'));
        $this->addColumn('traiding_investments_log', 'investment_id', $this->integer(11)->null()->defaultValue(null)->after('trading_account_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account','profit_summ');
        $this->dropColumn('traiding_investments_log', 'investment_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180417_083057_update_trading_account__table cannot be reverted.\n";

        return false;
    }
    */
}
