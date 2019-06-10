<?php

use yii\db\Migration;

/**
 * Class m190319_154716_remove_tables
 */
class m190319_154716_remove_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('investments');
        $this->dropTable('investments_cron');
        $this->dropTable('investments_daily_log');
        $this->dropTable('investments_perid_history');
        $this->dropTable('investments_plan');
        $this->dropTable('investment_delete_log');
        $this->dropTable('investment_protection');
        $this->dropTable('manager_reviews');
        $this->dropTable('photo');
        $this->dropTable('request_change_leverage');
        $this->dropTable('solution');
        $this->dropTable('solution_bonuses');
        $this->dropTable('solution_bonuses_log');
        $this->dropTable('solution_days_log');
        $this->dropTable('solution_period_log');
        $this->dropTable('solution_reviews');
        $this->dropTable('solution_traiding');
        $this->dropTable('trading_account');
        $this->dropTable('trading_account_change_history');
        $this->dropTable('trading_account_delete_request');
        $this->dropTable('trading_account_du_statistic');
        $this->dropTable('trading_account_exception');
        $this->dropTable('trading_account_history_terminal');
        $this->dropTable('trading_account_history_terminal_2');
        $this->dropTable('trading_account_yield_log');
        $this->dropTable('trading_offer');
        $this->dropTable('traiding_account_balace_history_day');
        $this->dropTable('traiding_investments_debt');
        $this->dropTable('traiding_investments_debt_log');
        $this->dropTable('traiding_investments_log');
        $this->dropTable('traiding_period_log');
        $this->dropTable('traiding_plan');

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
        echo "m190319_154716_remove_tables cannot be reverted.\n";

        return false;
    }
    */
}
