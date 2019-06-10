<?php

use yii\db\Migration;

/**
 * Class m180917_132342_investment_period_summ_for_show
 */
class m180917_132342_investment_period_summ_for_show extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'period_sum_show', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'period_sum_show');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180917_132342_investment_period_summ_for_show cannot be reverted.\n";

        return false;
    }
    */
}
