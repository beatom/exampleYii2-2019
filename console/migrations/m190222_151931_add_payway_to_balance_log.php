<?php

use yii\db\Migration;

/**
 * Class m190222_151931_add_payway_to_balance_log
 */
class m190222_151931_add_payway_to_balance_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_log', 'payway_id', $this->integer()->null()->defaultValue(null));
        $this->addColumn('payment_log', 'payway_id', $this->integer()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('balance_log', 'payway_id');
        $this->dropColumn('payment_log', 'payway_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190222_151931_add_payway_to_balance_log cannot be reverted.\n";

        return false;
    }
    */
}
