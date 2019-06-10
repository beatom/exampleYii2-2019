<?php

use yii\db\Migration;

/**
 * Class m180516_084241_request_delete_account_table
 */
class m180516_084241_request_delete_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('trading_account_delete_request', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'account_number' => $this->string(255)->notNull(),
            'status' => $this->integer(3)->notNull()->defaultValue(1)->comment('1 - новая заявка, 2 - выполнено'),
        ], $tableOptions . ' COMMENT "Заявки на удаление счетов для администратора"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('trading_account_delete_request');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180516_084241_request_delete_account_table cannot be reverted.\n";

        return false;
    }
    */
}
