<?php

use yii\db\Migration;

/**
 * Class m180427_113135_queue_mail
 */
class m180427_113135_queue_mail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        //соц сети
        $this->createTable('queue_mail',[
            'id' => $this->primaryKey(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_work' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'worked' => $this->smallInteger(1)->defaultValue(0),
            'from' => $this->string()->null()->defaultValue(null),
            'to' => $this->string()->null()->defaultValue(null),
            'subject' => $this->string()->null()->defaultValue(null),
            'template' => $this->string()->null()->defaultValue(null),
            'message' => $this->text()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Очередь для писем"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('queue_mail');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180427_113135_queue_mail cannot be reverted.\n";

        return false;
    }
    */
}
