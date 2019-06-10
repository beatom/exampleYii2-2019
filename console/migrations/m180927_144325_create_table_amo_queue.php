<?php

use yii\db\Migration;

/**
 * Class m180927_144325_create_table_amo_queue
 */
class m180927_144325_create_table_amo_queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('amo_queue',[
            'id' => $this->primaryKey(),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_work' =>  $this->dateTime()->null()->defaultValue(null),
            'worked' => $this->boolean()->notNull()->defaultValue(0),
            'task' => $this->string(255)->notNull(),
            'params' => $this->text()->null()->defaultValue(null),
            'additional_params' => $this->string(255)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Очередь запросов в AmoCrm"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('amo_queue');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180927_144325_create_table_amo_queue cannot be reverted.\n";

        return false;
    }
    */
}
