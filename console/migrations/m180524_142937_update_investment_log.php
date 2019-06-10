<?php

use yii\db\Migration;

/**
 * Class m180524_142937_update_investment_log
 */
class m180524_142937_update_investment_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments_daily_log', 'profit_summ', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments_daily_log', 'profit_summ');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180524_142937_update_investment_log cannot be reverted.\n";

        return false;
    }
    */
}
