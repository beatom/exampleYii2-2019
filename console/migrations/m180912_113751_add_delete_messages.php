<?php

use yii\db\Migration;

/**
 * Class m180912_113751_add_delete_messages
 */
class m180912_113751_add_delete_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('chat_message', 'deleted_at', $this->dateTime()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('chat_message', 'deleted_at');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180912_113751_add_delete_messages cannot be reverted.\n";

        return false;
    }
    */
}
