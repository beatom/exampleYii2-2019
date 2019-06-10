<?php

use yii\db\Migration;

/**
 * Class m180529_132217_solution_to_yield_log
 */
class m180529_132217_solution_to_yield_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account_yield_log', 'solution_id', $this->integer(11)->null()->defaultValue(null));
        $this->addColumn('solution', 'date_add', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('trading_account_yield_log', 'trading_account_id', $this->integer(11)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('trading_account_yield_log', 'solution_id');
        $this->dropColumn('solution', 'date_add');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180529_132217_solution_to_yield_log cannot be reverted.\n";

        return false;
    }
    */
}
