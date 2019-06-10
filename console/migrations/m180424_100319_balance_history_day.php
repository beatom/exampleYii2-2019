<?php

use yii\db\Migration;

/**
 * Class m180424_100319_balance_history_day
 */
class m180424_100319_balance_history_day extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_account_balace_history_day', 'profit_in_terminal', $this->double(11)->notNull()->defaultValue(0)->comment('$'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180424_100319_balance_history_day cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180424_100319_balance_history_day cannot be reverted.\n";

        return false;
    }
    */
}
