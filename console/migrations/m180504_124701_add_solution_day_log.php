<?php

use yii\db\Migration;

/**
 * Class m180504_124701_add_solution_day_log
 */
class m180504_124701_add_solution_day_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('solution_days_log',[
            'id' => $this->primaryKey(),
            'solution_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'profit' => $this->double()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "логи готового решения по дням"');

        $this->addColumn('solution', 'profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('solution', 'six_month_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('solution', 'one_month_profit', $this->double()->notNull()->defaultValue(0));
        $this->addColumn('solution', 'one_week_profit', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('solution_days_log');
        $this->dropColumn('solution', 'profit');
        $this->dropColumn('solution', 'six_month_profit');
        $this->dropColumn('solution', 'one_month_profit');
        $this->dropColumn('solution', 'one_week_profit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180504_124701_add_solution_day_log cannot be reverted.\n";

        return false;
    }
    */
}
