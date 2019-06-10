<?php

use yii\db\Migration;

/**
 * Class m180216_100303_create_chat_tables
 */
class m180216_100303_create_chat_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('chat',[
            'id' => $this->primaryKey(),
            'user_1_id' => $this->integer(11)->notNull(),
            'user_2_id' => $this->integer(11)->notNull(),
            'last_update' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('chat_message',[
            'id' => $this->primaryKey(),
            'chat_id' => $this->integer(11)->notNull(),
            'date_send' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'user_id' => $this->integer(11)->notNull(),
            'text' => $this->text()->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createTable('chat_mass_messages',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'as_admin' => $this->boolean()->notNull()->defaultValue(0),
            'date_send' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'text' => $this->text()->notNull(),
        ], $tableOptions);

        $this->addColumn('user', 'unread_messages', $this->integer(11)->notNull()->defaultValue(0));
        $this->addColumn('user', 'unread_messages_notified', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('chat');
        $this->dropTable('chat_message');
        $this->dropTable('chat_mass_messages');
        $this->dropColumn('user', 'unread_messages');
        $this->dropColumn('user', 'unread_messages_notified');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180216_100303_create_chat_tables cannot be reverted.\n";

        return false;
    }
    */
}
