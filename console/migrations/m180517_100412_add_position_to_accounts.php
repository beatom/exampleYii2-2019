<?php

use yii\db\Migration;

/**
 * Class m180517_100412_add_position_to_accounts
 */
class m180517_100412_add_position_to_accounts extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'position', $this->integer(11)->notNull()->unsigned() );
        $this->addColumn('trading_account', 'kf_sortino', $this->double()->notNull()->defaultValue(0) );

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('trading_account_du_statistic', [
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'profit' => $this->double()->notNull()->defaultValue(0),
            'trader_balance' => $this->string()->null()->defaultValue(null),
            'one_month_profit' => $this->double()->notNull()->defaultValue(0),
            'one_week_profit' => $this->double()->notNull()->defaultValue(0),
            'chart' => $this->text()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Данные для отображения в списке ДУ"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'position');
        $this->dropColumn('trading_account', 'kf_sortino');
        $this->dropTable('trading_account_du_statistic');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180517_100412_add_position_to_accounts cannot be reverted.\n";

        return false;
    }
    */
}
