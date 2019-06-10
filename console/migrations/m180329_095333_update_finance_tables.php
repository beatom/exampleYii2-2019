<?php

use yii\db\Migration;

/**
 * Class m180329_095333_update_finance_tables
 */
class m180329_095333_update_finance_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('traiding_period_log', 'summ_start', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('traiding_period_log', 'summ_add', $this->double(11)->notNull()->defaultValue(0)->comment('суммы, которые добавили/вывели с понедельника по пятницу'));
        $this->alterColumn('traiding_period_log', 'summ_end', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('traiding_period_log', 'profit', $this->double(11)->notNull()->defaultValue(0));

        $this->alterColumn('investments', 'profit', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('investments', 'summ_start', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('investments', 'summ_current', $this->double(11)->notNull()->defaultValue(0));

        $this->alterColumn('traiding_investments_log', 'summ', $this->double(11)->notNull()->defaultValue(0));

        $this->alterColumn('trading_account', 'summ', $this->double(11)->notNull()->defaultValue(0));
        
        $this->alterColumn('investments_perid_history', 'summ_start', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('investments_perid_history', 'summ_end', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('investments_perid_history', 'profit', $this->double(11)->notNull()->defaultValue(0));

        $this->alterColumn('traiding_account_balace_history_day', 'summ', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('traiding_account_balace_history_day', 'profit', $this->double(11)->notNull()->defaultValue(0));

        $this->alterColumn('traiding_investments_debt', 'summ', $this->double(11)->notNull()->defaultValue(0));

        $this->alterColumn('traiding_investments_debt', 'summ', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('traiding_investments_debt_log', 'summ', $this->double(11)->notNull()->defaultValue(0));

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
        echo "m180329_095333_update_finance_tables cannot be reverted.\n";

        return false;
    }
    */
}
