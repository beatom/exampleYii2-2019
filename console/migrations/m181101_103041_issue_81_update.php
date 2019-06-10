<?php

use yii\db\Migration;

/**
 * Class m181101_103041_issue_81_update
 */
class m181101_103041_issue_81_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'block_withdraw', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('trading_account', 'bonus_start_date', $this->dateTime()->null()->defaultValue(null));
        $this->addColumn('trading_account', 'bonus_worked', $this->boolean()->null()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'block_withdraw');
        $this->dropColumn('trading_account', 'bonus_start_date');
        $this->dropColumn('trading_account', 'bonus_worked');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181101_103041_issue_81_update cannot be reverted.\n";

        return false;
    }
    */
}
