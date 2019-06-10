<?php

use yii\db\Migration;

/**
 * Class m190321_005321_objectives
 */
class m190321_005321_objectives extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('objective',[
            'id' => $this->primaryKey(),
            'max_sum' =>  $this->float(11)->notNull(),
        ], $tableOptions . ' COMMENT "Цели"');

        $this->createTable('objective_stage',[
            'id' => $this->primaryKey(),
            'objective_id' =>  $this->integer(11)->notNull(),
            'stage' => $this->integer(3)->notNull()->defaultValue(0),
            'title' => $this->string(255)->null()->defaultValue(null),
            'title_en' => $this->string(255)->null()->defaultValue(null),
            'description' => $this->text()->null()->defaultValue(null),
            'description_en' => $this->text()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Цели пользователей"');

        $this->createTable('user_objectives',[
            'id' => $this->primaryKey(),
            'user_id' =>  $this->integer(11)->notNull(),
            'comment' => $this->string(255)->null()->defaultValue(null),
            'sum_start' => $this->float(11)->notNull(),
            'sum_end' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_end' => $this->dateTime()->null()->defaultValue(NULL),
        ], $tableOptions . ' COMMENT "Цели пользователей"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('objective');
        $this->dropTable('objective_stage');
        $this->dropTable('user_objectives');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190321_005321_objectives cannot be reverted.\n";

        return false;
    }
    */
}
