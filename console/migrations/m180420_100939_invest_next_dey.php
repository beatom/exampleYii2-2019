<?php

use yii\db\Migration;

/**
 * Class m180420_100939_invest_next_dey
 */
class m180420_100939_invest_next_dey extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_account_balace_history_day', 'summ_invest', $this->double()->notNull()->defaultValue(0));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('investments_cron',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'summ' => $this->integer(11)->notNull(),
            'balance_bonus_log_id' => $this->integer(11)->null(),
            'executed' => $this->smallInteger(1)->notNull()->defaultValue(0),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . ' COMMENT "Крон для инвестиций на следущий день"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('traiding_account_balace_history_day', 'summ_invest');
        $this->dropTable('investments_cron');

        return true;
    }

}
