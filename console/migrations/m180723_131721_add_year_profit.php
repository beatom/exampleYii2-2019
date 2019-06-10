<?php

use yii\db\Migration;

/**
 * Class m180723_131721_add_year_profit
 */
class m180723_131721_add_year_profit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'one_year_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('trading_account_du_statistic', 'one_year_profit', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'one_year_profit');
        $this->dropColumn('trading_account_du_statistic', 'one_year_profit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180723_131721_add_year_profit cannot be reverted.\n";

        return false;
    }
    */
}
