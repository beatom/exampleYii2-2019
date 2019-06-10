<?php

use yii\db\Migration;

/**
 * Class m180521_111438_investment_log_update
 */
class m180521_111438_investment_log_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments_daily_log', 'summ_withdraw', $this->double()->notNull()->defaultValue(0)->after('summ_add'));
        $this->addColumn('traiding_account_balace_history_day', 'summ_withdraw', $this->double()->notNull()->defaultValue(0)->after('summ_add'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments_daily_log', 'summ_withdraw');
        $this->dropColumn('traiding_account_balace_history_day', 'summ_withdraw');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180521_111438_investment_log_update cannot be reverted.\n";

        return false;
    }
    */
}
