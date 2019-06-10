<?php

use yii\db\Migration;

/**
 * Class m180523_225954_update_du_statistic
 */
class m180523_225954_update_du_statistic extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account_du_statistic', 'account_balance', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('trading_account_du_statistic', 'investor_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('trading_account_du_statistic', 'six_month_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('trading_account_du_statistic', 'trader_balance_single', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account_du_statistic', 'trader_balance_single');
        $this->dropColumn('trading_account_du_statistic', 'account_balance');
        $this->dropColumn('trading_account_du_statistic', 'investor_profit');
        $this->dropColumn('trading_account_du_statistic', 'six_month_profit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180523_225954_update_du_statistic cannot be reverted.\n";

        return false;
    }
    */
}
