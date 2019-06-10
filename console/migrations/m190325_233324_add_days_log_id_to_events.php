<?php

use yii\db\Migration;

/**
 * Class m190325_233324_add_days_log_id_to_events
 */
class m190325_233324_add_days_log_id_to_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       // $this->addColumn('days_log', 'complete', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('events', 'days_log_id', $this->integer(11)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       // $this->dropColumn('days_log', 'complete');
        $this->dropColumn('events', 'days_log_id');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190325_233324_add_days_log_id_to_events cannot be reverted.\n";

        return false;
    }
    */
}
