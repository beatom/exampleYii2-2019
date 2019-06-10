<?php

use yii\db\Migration;

/**
 * Class m180515_083443_update_trading_period_log
 */
class m180515_083443_update_trading_period_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
            $this->addColumn('traiding_period_log', 'real_date_end', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
            $this->dropColumn('traiding_period_log', 'summ_start');
            $this->dropColumn('traiding_period_log', 'summ_add');
            $this->dropColumn('traiding_period_log', 'summ_end');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('traiding_period_log', 'real_date_end');
        $this->addColumn('traiding_period_log', 'summ_start', $this->double()->null()->defaultValue('null'));
        $this->addColumn('traiding_period_log', 'summ_add', $this->double()->null()->defaultValue('null'));
        $this->addColumn('traiding_period_log', 'summ_end', $this->double()->null()->defaultValue('null'));
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180515_083443_update_trading_period_log cannot be reverted.\n";

        return false;
    }
    */
}
