<?php

use yii\db\Migration;

/**
 * Class m180508_104716_update_trading_account_day_table
 */
class m180508_104716_update_trading_account_day_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_account_balace_history_day', 'intermediate_profit', $this->double()->notNull()->defaultValue(0)->after('profit')->comment('Временный % прибыли'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('traiding_account_balace_history_day', 'intermediate_profit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180508_104716_update_trading_account_day_table cannot be reverted.\n";

        return false;
    }
    */
}
