<?php

use yii\db\Migration;

/**
 * Handles the creation of table `trading_rollover`.
 */
class m180403_121514_create_trading_rollover_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_period_log', 'profit_summ', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('traiding_period_log', 'honorar', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('traiding_period_log', 'responsibility', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('balance_bonus_log', 'expired', $this->boolean()->notNull()->defaultValue(0));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('trading_account_yield_log',[
            'id' => $this->primaryKey(),
            'date_start' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_end' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'type' => $this->integer(11)->notNull()->comment('1 - неделя, 2 - месяц'),
            'trading_account_id' => $this->integer(11)->notNull(),
            'profit' => $this->double()->notNull()->defaultValue(0),
            'percent' => $this->double()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "доходность счета ДУ по неделям и месяцам"');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('traiding_period_log', 'profit_summ');
        $this->dropColumn('traiding_period_log', 'honorar');
        $this->dropColumn('traiding_period_log', 'responsibility');
        $this->dropColumn('balance_bonus_log', 'expired');
        $this->dropTable('trading_account_yield_log');

        return true;
    }
}
