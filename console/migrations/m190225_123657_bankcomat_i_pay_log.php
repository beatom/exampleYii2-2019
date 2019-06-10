<?php

use yii\db\Migration;

/**
 * Class m190225_123657_bankcomat_i_pay_log
 */
class m190225_123657_bankcomat_i_pay_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_log', 'to_execute', $this->boolean()->notNull()->defaultValue(0));
        $this->addColumn('payment_log', 'to_execute_time', $this->dateTime()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment_log', 'to_execute');
        $this->dropColumn('payment_log', 'to_execute_time');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190225_123657_bankcomat_i_pay_log cannot be reverted.\n";

        return false;
    }
    */
}
