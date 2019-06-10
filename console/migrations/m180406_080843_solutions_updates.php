<?php

use yii\db\Migration;

/**
 * Class m180406_080843_solutions_updates
 */
class m180406_080843_solutions_updates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('solution_traiding', 'manager_profit');
        $this->alterColumn('solution_period_log', 'profit_total', $this->double()->notNull()->defaultValue(0));


        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('api_error_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->null()->defaultValue(null),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'action' => $this->string(255)->null()->defaultValue(null)->comment('запрос'),
            'data' => $this->text()->null()->defaultValue(null)->comment('параметры запроса'),
            'answer' => $this->text()->null()->defaultValue(null)->comment('ответ терминала'),
        ], $tableOptions . ' COMMENT "ошибки запросов терминала"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('api_error_log');
        $this->addColumn('solution_traiding', 'manager_profit', $this->double()->notNull()->defaultValue(0));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180406_080843_solutions_updates cannot be reverted.\n";

        return false;
    }
    */
}
