<?php

use yii\db\Migration;

/**
 * Class m190417_132854_change_chat_templates
 */
class m190417_132854_change_chat_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('chat');
        $this->dropColumn('chat_message', 'chat_id');
        $this->dropColumn('chat_message', 'status');
        $this->dropColumn('chat_message', 'date_send');

        $this->addColumn('chat_message', 'date_add', $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('chat_message', 'parent_id', $this->integer(11)->null()->defaultValue(null));
        $this->addColumn('chat_message', 'responsible_id', $this->integer(11)->null()->defaultValue(null));

        $this->dropColumn('user_ban', 'permanent');
        $this->addColumn('user_ban', 'comment', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190417_132854_change_chat_templates cannot be reverted.\n";

        return false;
    }
    */
}
