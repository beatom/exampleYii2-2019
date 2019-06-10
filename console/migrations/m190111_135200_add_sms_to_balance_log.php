<?php

use yii\db\Migration;

/**
 * Class m190111_135200_add_sms_to_balance_log
 */
class m190111_135200_add_sms_to_balance_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_log', 'sms', $this->boolean()->defaultValue(false)->comment('Было использовано смс при выводе средств'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('balance_log', 'sms');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190111_135200_add_sms_to_balance_log cannot be reverted.\n";

        return false;
    }
    */
}
