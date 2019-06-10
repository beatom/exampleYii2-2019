<?php

use yii\db\Migration;

/**
 * Class m190425_221956_grand_parent_chat_message
 */
class m190425_221956_grand_parent_chat_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('chat_message', 'branch_id', $this->integer(11)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('chat_message', 'branch_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190425_221956_grand_parent_chat_message cannot be reverted.\n";

        return false;
    }
    */
}
