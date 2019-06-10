<?php

use yii\db\Migration;

/**
 * Class m180614_084052_trading_account_update_show
 */
class m180614_084052_trading_account_update_show extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'show', $this->boolean()->notNull()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'show');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180614_084052_trading_account_update_show cannot be reverted.\n";

        return false;
    }
    */
}
