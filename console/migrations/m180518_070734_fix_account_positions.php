<?php

use yii\db\Migration;

/**
 * Class m180518_070734_fix_account_positions
 */
class m180518_070734_fix_account_positions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('trading_account', 'position', $this->integer(11)->notNull()->defaultValue(0)->unsigned() );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180518_070734_fix_account_positions cannot be reverted.\n";

        return false;
    }
    */
}
