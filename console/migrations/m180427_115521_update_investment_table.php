<?php

use yii\db\Migration;

/**
 * Class m180427_115521_update_investment_table
 */
class m180427_115521_update_investment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('investments', 'bonus_money', $this->integer(11)->null()->defaultValue(null)->comment('id bonus_log'));
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
        echo "m180427_115521_update_investment_table cannot be reverted.\n";

        return false;
    }
    */
}
