<?php

use yii\db\Migration;

/**
 * Class m180605_095121_investment_period_profit
 */
class m180605_095121_investment_period_profit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'profit_period', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'profit_period');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180605_095121_investment_period_profit cannot be reverted.\n";

        return false;
    }
    */
}
