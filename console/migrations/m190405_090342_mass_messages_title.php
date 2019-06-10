<?php

use yii\db\Migration;

/**
 * Class m190405_090342_mass_messages_title
 */
class m190405_090342_mass_messages_title extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('chat_mass_messages', 'title', $this->string(255)->null()->defaultValue(NULL));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('chat_mass_messages', 'title');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190405_090342_mass_messages_title cannot be reverted.\n";

        return false;
    }
    */
}
