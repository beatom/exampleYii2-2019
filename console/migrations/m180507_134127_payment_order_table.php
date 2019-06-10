<?php

use yii\db\Migration;

/**
 * Class m180507_134127_payment_order_table
 */
class m180507_134127_payment_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('payment_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'size' => $this->double(11)->notNull()->unsigned(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'payment_system' => $this->integer(11)->notNull()->unsigned(),
            'system_payment_id' => $this->string(255)->null()->defaultValue(null),
            'completed' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Лог платежей"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('payment_log');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180507_134127_payment_order_table cannot be reverted.\n";

        return false;
    }
    */
}
