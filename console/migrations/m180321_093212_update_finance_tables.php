<?php

use yii\db\Migration;

/**
 * Class m180321_093212_update_finance_tables
 */
class m180321_093212_update_finance_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'is_active', $this->boolean()->notNull()->defaultValue(1)->comment('0 - для счетов Ду, задается изначельно в ожидании заполнении оферты и пополнения трейдером на 500$'));
        $this->alterColumn('traiding_investments_log', 'solution_id', $this->integer(11)->null()->defaultValue(NULL));
        $this->alterColumn('traiding_investments_log', 'balance_bonus_log_id', $this->integer(11)->null()->defaultValue(NULL));
        $this->alterColumn('traiding_investments_log', 'profit', $this->double(11)->notNull()->defaultValue(0));
        $this->alterColumn('traiding_investments_log', 'datetime_add', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->alterColumn('traiding_investments_log', 'type_invest', $this->boolean()->notNull()->defaultValue(1)->comment('0 - бонусы, 1 - реальные деньги'));
        $this->addColumn('traiding_investments_log', 'summ', $this->integer(11)->notNull()->defaultValue(0)->comment('если сумма с минусом, то вывод денег'));
        $this->alterColumn('traiding_investments_log', 'type', $this->integer(3)->notNull());
        $this->alterColumn('traiding_investments_log', 'status', $this->integer(3)->notNull()->comment('1 - выполнено, 2 - в обработке, 3 - отменена'));

        $this->insert('options', [
            'key' => 'trust-management-min-deposit',
            'value' => 500,
            'description' => 'Сумма которую должен инвестировать трейдер в счет ДУ для начала торговли',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('trading_account', 'is_active');
        $this->dropColumn('traiding_investments_log', 'summ');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180321_093212_update_finance_tables cannot be reverted.\n";

        return false;
    }
    */
}
