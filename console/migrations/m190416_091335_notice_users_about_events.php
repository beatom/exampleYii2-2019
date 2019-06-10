<?php

use yii\db\Migration;

/**
 * Class m190416_091335_notice_users_about_events
 */
class m190416_091335_notice_users_about_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user','events_notice', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('user', 'events_notice');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190416_091335_notice_users_about_events cannot be reverted.\n";

        return false;
    }
    */
}
