<?php

use yii\db\Migration;

/**
 * Class m181212_112959_add_trading_account_special_bonus
 */
class m181212_112959_add_trading_account_special_bonus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'bonus_type', $this->integer(3)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'bonus_type');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181212_112959_add_trading_account_special_bonus cannot be reverted.\n";

        return false;
    }
    */
}
