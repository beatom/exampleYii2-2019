<?php

use yii\db\Migration;

/**
 * Class m180411_133013_change_history
 */
class m180411_133013_change_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('trading_account_history_terminal', 'SL', $this->float(11));
        $this->alterColumn('trading_account_history_terminal', 'TP', $this->float(11));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180411_133013_change_history cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180411_133013_change_history cannot be reverted.\n";

        return false;
    }
    */
}
