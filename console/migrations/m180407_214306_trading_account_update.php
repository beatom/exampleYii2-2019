<?php

use yii\db\Migration;

/**
 * Class m180407_214306_trading_account_update
 */
class m180407_214306_trading_account_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'utip_account_id', $this->integer(11)->null());
        $this->addColumn('trading_account', 'investor_password', $this->string(255)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'utip_account_id');
        $this->dropColumn('trading_account', 'investor_password');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180407_214306_trading_account_update cannot be reverted.\n";

        return false;
    }
    */
}
