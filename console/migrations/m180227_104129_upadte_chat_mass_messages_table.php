<?php

use yii\db\Migration;

/**
 * Class m180227_104129_upadte_chat_mass_messages_table
 */
class m180227_104129_upadte_chat_mass_messages_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('chat_mass_messages', 'as_admin');
        $this->addColumn('chat_mass_messages', 'sender_id', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180227_104129_upadte_chat_mass_messages_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180227_104129_upadte_chat_mass_messages_table cannot be reverted.\n";

        return false;
    }
    */
}
