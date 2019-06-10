<?php

use yii\db\Migration;

/**
 * Class m180402_083718_change_balance_history_day_table
 */
class m180402_083718_change_balance_history_day_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('traiding_account_balace_history_day', 'summ_start', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('traiding_account_balace_history_day', 'summ_add', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('traiding_account_balace_history_day', 'summ_end', $this->double()->notNull()->defaultValue(0));
        $this->dropColumn('traiding_account_balace_history_day', 'summ');

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('trading_account_history_terminal', [
            'ID' => $this->primaryKey(),
            'SPEND_BONUS' => $this->float(11)->notNull(),
            'VOLUME' => $this->string(255)->notNull(),
            'OPEN_DATE' => $this->integer(11)->notNull(),
            'CLOSE_DATE' => $this->integer(11)->notNull(),
            'OPEN_PRICE' => $this->float(11)->notNull(),
            'CLOSE_PRICE' => $this->float(11)->notNull(),
            'POSITION_TYPE' => $this->string(255)->notNull(),
            'SWAP' => $this->float(11)->notNull(),
            'COMMISSION' => $this->float(11)->notNull(),
            'PROFIT' => $this->float(11)->notNull(),
            'SYMBOL' => $this->string(255)->notNull(),
            'MARGIN' => $this->float(11)->notNull(),
            'BONUS' => $this->float(11)->notNull(),
        ], $tableOptions . ' COMMENT "История сделок по аккаунту из терминала"');

    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('traiding_account_balace_history_day', 'summ', $this->double()->notNull()->defaultValue(0));
        $this->dropColumn('traiding_account_balace_history_day', 'summ_start');
        $this->dropColumn('traiding_account_balace_history_day', 'summ_add');
        $this->dropColumn('traiding_account_balace_history_day', 'summ_end');
        $this->dropTable('trading_account_history_terminal');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180402_083718_change_balance_history_day_table cannot be reverted.\n";

        return false;
    }
    */
}
