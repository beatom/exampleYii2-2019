<?php

use yii\db\Migration;

/**
 * Class m180315_112017_change_investments_table
 */
class m180315_112017_change_investments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'trading_account_id', $this->integer(11)->notNull());
        $this->addColumn('trading_account', 'is_du', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'trading_account_id');
        $this->dropColumn('trading_account', 'is_du');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180315_112017_change_investments_table cannot be reverted.\n";

        return false;
    }
    */
}
