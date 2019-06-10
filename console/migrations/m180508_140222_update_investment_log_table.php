<?php

use yii\db\Migration;

/**
 * Class m180508_140222_update_investment_log_table
 */
class m180508_140222_update_investment_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('traiding_investments_log', 'profit');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('traiding_investments_log', 'profit', $this->double()->notNull()->defaultValue(0));
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180508_140222_update_investment_log_table cannot be reverted.\n";

        return false;
    }
    */
}
