<?php

use yii\db\Migration;

/**
 * Class m180302_132049_finance_tables
 */
class m180302_132049_finance_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('trading_account',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'type_account' => $this->integer(11)->notNull()->comment('mini/standart/profi'),
            'is_islam' => $this->boolean()->notNull()->defaultValue(0),
            'account_number' => $this->string(255)->notNull(),
            'password' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'leverage' => $this->string(255)->notNull()->comment('плечо'),
            'honorar' => $this->integer(3)->notNull()->comment('гонорар управляющему, в %'),
            'honorar_partner' => $this->integer(3)->notNull()->comment('вознаграждение партнеру, %'),
            'responsibility' => $this->integer(3)->notNull()->comment('отвественность управляющего, %'),
            'history_show' => $this->boolean()->notNull()->defaultValue(1)->comment('разрешать показывать историю сделок'),
            'trading_period' => $this->integer(11)->notNull()->comment('торговый период, недель'),
            'summ' => $this->integer(11)->notNull()->defaultValue(0)->comment('текущая сумма всех денег на счете'),
            'minimum_deposit' => $this->integer(11)->notNull()->comment('минимальный депозит'),
            'pamm' => $this->boolean()->notNull()->defaultValue(1)->comment('1 - люди могут вкладывать деньги'),
        ], $tableOptions . ' COMMENT "торговые аккаунты"');

        $this->createTable('trading_account_demo',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'type_account' => $this->integer(11)->notNull()->comment('mini/standart/profi'),
            'is_islam' => $this->boolean()->notNull()->defaultValue(0),
            'summ' => $this->integer(11)->notNull()->comment('вернет терминал'),
            'account_number' => $this->string(255)->notNull()->comment('сумма на део счете'),
            'password' => $this->string(255)->notNull(),
            'name' => $this->string(255)->notNull(),
            'leverage' => $this->string(255)->notNull()->comment('плечо'),
        ], $tableOptions . ' COMMENT "демо торговые аккаунты"');

        $this->createTable('investments',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'bonus_money' => $this->boolean()->notNull()->defaultValue(0)->comment('отдельно учитываем бонусные деньги'),
            'summ_start' => $this->integer(11)->notNull()->defaultValue(0),
            'summ_current' => $this->integer(11)->notNull()->defaultValue(0),
            'profit' => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "история доходности инвестора по торговым периодам"');

        $this->createTable('investments_perid_history',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'traiding_period_log' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'bonus_money' => $this->boolean()->notNull()->defaultValue(1)->comment('отдельно учитываем бонусные деньги'),
            'summ_start' => $this->integer(11)->notNull(),
            'summ_end' => $this->integer(11)->notNull(),
            'profit' => $this->integer(11)->notNull(),
        ], $tableOptions . ' COMMENT "история доходности инвестора по торговым периодам"');

        $this->createTable('traiding_account_balace_history_day',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'date' => $this->dateTime()->notNull(),
            'summ' => $this->integer(11)->notNull(),
            'profit' => $this->integer(11)->notNull(),
        ], $tableOptions . ' COMMENT "баланс каждого счета по дням (на основе investments_log)"');

        $this->createTable('traiding_period_log',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'date_start' => $this->dateTime()->notNull(),
            'date_end' => $this->dateTime()->null(),
            'summ_start' => $this->integer(11)->notNull(),
            'summ_add' => $this->integer(11)->notNull()->comment('суммы, которые добавили/вывели с понедельника по пятницу'),
            'summ_end' => $this->integer(11)->notNull(),
            'profit' => $this->integer(11)->notNull(),
        ], $tableOptions . ' COMMENT "история торговых периодов, заполняется понедельник и при каждом пополнении, в субботу создается новая строка"');


        $this->createTable('traiding_investments_log',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'solution_id' => $this->integer(11)->notNull()->comment('если поле заполнено, то инвестор вложил деньги в "готовое решение"'),
            'user_id' => $this->integer(11)->notNull(),
            'datetime_add' => $this->dateTime()->null(),
            'type_invest' => $this->integer(1)->notNull()->comment('real/bonus'),
            'balance_bonus_log_id' => $this->integer(11)->notNull()->comment('id записи в таблице с бонусами'),
            'profit' => $this->integer(11)->notNull()->comment('сколько денег заработала данная оперция'),
            'type' => $this->integer(1)->notNull()->comment('real/bonus'),
            'status' => $this->boolean()->notNull()->defaultValue(0)->comment('в ожидании / обработано'),
        ], $tableOptions);

        $this->createTable('traiding_investments_debt',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'summ' => $this->integer(11)->notNull(),
        ], $tableOptions . ' COMMENT "таблица со списком долгов управляющего перед инвесторами, тут хранится общая сумма долгов"');

        $this->createTable('traiding_investments_debt_log',[
            'id' => $this->primaryKey(),
            'traiding_investments_debt_id' => $this->integer(11)->notNull(),
            'traiding_period_log_id' => $this->integer(11)->notNull()->comment('ID торгового периода, в котором возникла или была погашена задолженность'),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->comment('дата операции'),
            'summ' => $this->integer(11)->notNull()->comment('сумма с минусом - увеличение задолженности, сумма с плюсом - погашение задолженности'),
        ], $tableOptions . ' COMMENT "операции увеличения/уменьшения долга"');

        $this->createTable('balance_bonus_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'summ' => $this->integer(11)->notNull(),
            'summ_now' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_end' => $this->dateTime()->null(),
            'description' => $this->text()->notNull(),
        ], $tableOptions);

        $this->createTable('solution',[
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null(),
        ], $tableOptions. ' COMMENT "готовое решение"');

        $this->createTable('solution_traiding',[
            'id' => $this->primaryKey(),
            'solution_id' => $this->integer()->notNull(),
            'trading_account_id' => $this->integer()->notNull(),
            'proportion' => $this->integer()->notNull(),
            'manager_profit' => $this->integer()->notNull()->comment('награда управляющему'),
        ], $tableOptions. ' COMMENT "список счетов ДУ в готовом решении"');

        $this->createTable('solution_period_log',[
            'id' => $this->primaryKey(),
            'solution_id' => $this->integer()->notNull(),
            'date_start' => $this->dateTime()->null(),
            'date_end' => $this->dateTime()->null(),
            'profit_total' => $this->integer()->notNull()->comment('доходность за этот период'),
        ], $tableOptions . ' COMMENT "таблица опций"');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('trading_account');
        $this->dropTable('trading_account_demo');
        $this->dropTable('investments');
        $this->dropTable('investments_perid_history');
        $this->dropTable('traiding_account_balace_history_day');
        $this->dropTable('traiding_period_log');
        $this->dropTable('traiding_investments_log');
        $this->dropTable('traiding_investments_debt');
        $this->dropTable('traiding_investments_debt_log');
        $this->dropTable('balance_bonus_log');
        $this->dropTable('solution');
        $this->dropTable('solution_traiding');
        $this->dropTable('solution_period_log');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180302_132049_finance_tables cannot be reverted.\n";

        return false;
    }
    */
}
