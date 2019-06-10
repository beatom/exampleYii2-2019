<?php

use yii\db\Migration;

/**
 * Class m190405_151608_chat_template_title
 */
class m190405_151608_chat_template_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('chat_template', 'title', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('chat_template', 'title');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190405_151608_chat_template_title cannot be reverted.\n";

        return false;
    }
    */
}
