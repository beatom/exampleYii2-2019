<?php

use yii\db\Migration;

/**
 * Class m180502_101343_update_investments_table
 */
class m180502_101343_update_investments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('investments', 'trading_account_id', $this->integer(11)->null()->defaultValue(null));
        $this->addColumn('investments', 'solution_id', $this->integer(11)->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'solution_idphp');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180502_101343_update_investments_table cannot be reverted.\n";

        return false;
    }
    */
}
