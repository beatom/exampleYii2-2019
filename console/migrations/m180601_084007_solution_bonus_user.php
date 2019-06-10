<?php

use yii\db\Migration;

/**
 * Class m180601_084007_solution_bonus_user
 */
class m180601_084007_solution_bonus_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('solution_bonuses_log', [
            'id' => $this->primaryKey(),
            'solution_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'invest_summ' => $this->double()->notNull()->defaultValue(0),
            'bonus_summ' => $this->double()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Лог начислений бонусов пользователям за инвестирование в готовое решение"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('solution_bonuses_log');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180601_084007_solution_bonus_user cannot be reverted.\n";

        return false;
    }
    */
}
