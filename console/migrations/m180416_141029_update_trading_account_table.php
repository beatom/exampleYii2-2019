<?php

use yii\db\Migration;

/**
 * Class m180416_141029_update_trading_account_table
 */
class m180416_141029_update_trading_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'six_month_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('trading_account', 'one_month_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('trading_account', 'one_week_profit', $this->double()->notNull()->defaultValue(0));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('request_change_leverage', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'trading_account_id' => $this->integer(11)->notNull(),
            'new_leverage' => $this->integer(11)->notNull(),
            'status' => $this->integer(3)->notNull()->defaultValue(1)->comment('1 - новая заявка, 2 - выполнено, 3 - отменено'),
        ], $tableOptions . ' COMMENT "Заявки на смену кредитного плеча торгового счета"');



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'six_month_profit');
        $this->dropColumn('trading_account', 'one_month_profit');
        $this->dropColumn('trading_account', 'one_week_profit');
        $this->dropTable('request_change_leverage');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180416_141029_update_trading_account_table cannot be reverted.\n";

        return false;
    }
    */
}
