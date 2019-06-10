<?php

use yii\db\Migration;

/**
 * Class m190409_091744_events_show_field
 */
class m190409_091744_events_show_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('events','show',$this->boolean()->notNull()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('events','show');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190409_091744_events_show_field cannot be reverted.\n";

        return false;
    }
    */
}
