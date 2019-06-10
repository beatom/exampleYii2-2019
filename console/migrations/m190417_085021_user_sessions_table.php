<?php

use yii\db\Migration;

/**
 * Class m190417_085021_user_sessions_table
 */
class m190417_085021_user_sessions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_ip_log','session_id', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_ip_log', 'session_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190417_085021_user_sessions_table cannot be reverted.\n";

        return false;
    }
    */
}
