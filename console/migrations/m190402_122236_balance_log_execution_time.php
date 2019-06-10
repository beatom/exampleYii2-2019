<?php

use yii\db\Migration;

/**
 * Class m190402_122236_balance_log_execution_time
 */
class m190402_122236_balance_log_execution_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_log', 'execution_time', $this->dateTime()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('balance_log', 'execution_time');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190402_122236_balance_log_execution_time cannot be reverted.\n";

        return false;
    }
    */
}
