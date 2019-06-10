<?php

use yii\db\Migration;

/**
 * Class m190508_130622_user_events_complete
 */
class m190508_130622_user_events_complete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'events_complete', $this->boolean()->notNull()->defaultValue(false));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user', 'events_complete');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190508_130622_user_events_complete cannot be reverted.\n";

        return false;
    }
    */
}
