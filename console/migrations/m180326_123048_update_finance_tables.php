<?php

use yii\db\Migration;

/**
 * Class m180326_123048_update_finance_tables
 */
class m180326_123048_update_finance_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'profit', $this->double(11)->notNull()->defaultValue(0));
        $this->addColumn('trading_account', 'rating', $this->integer(11)->null()->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'profit');
        $this->dropColumn('trading_account', 'rating');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180326_123048_update_finance_tables cannot be reverted.\n";

        return false;
    }
    */
}
