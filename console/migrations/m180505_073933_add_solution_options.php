<?php

use yii\db\Migration;

/**
 * Class m180505_073933_add_solution_options
 */
class m180505_073933_add_solution_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('solution_bonuses',[
            'id' => $this->primaryKey(),
            'from' => $this->integer(11)->notNull()->unique()->unsigned(),
            'size' => $this->integer(11)->notNull()->unsigned(),
        ], $tableOptions . ' COMMENT "Бонусы при инвестировании в готовое решение"');

        $this->dropColumn('solution_period_log', 'profit_total');
        $this->addColumn('solution_period_log', 'counted', $this->boolean()->notNull()->defaultValue(0));

        $this->batchInsert('solution_bonuses', [ 'from', 'size'], [
            [
                'from' => '1000',
                'size' => '100',
            ],
            [
                'from' => '5000',
                'size' => '250',
            ],
            [
                'from' => '10000',
                'size' => '500',
            ],
            [
                'from' => '20000',
                'size' => '1000',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('solution_period_log', 'counted');
        $this->addColumn('solution_period_log', 'profit_total', $this->boolean()->notNull()->defaultValue(0));
        $this->dropTable('solution_bonuses');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180505_073933_add_solution_options cannot be reverted.\n";

        return false;
    }
    */
}
