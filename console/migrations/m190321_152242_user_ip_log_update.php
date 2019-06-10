<?php

use yii\db\Migration;

/**
 * Class m190321_152242_user_ip_log_update
 */
class m190321_152242_user_ip_log_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_ip_log', 'browser', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('user_ip_log', 'browser_version', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('user_ip_log', 'os', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('user_ip_log', 'os_version', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_ip_log', 'browser');
        $this->dropColumn('user_ip_log', 'browser_version');
        $this->dropColumn('user_ip_log', 'os');
        $this->dropColumn('user_ip_log', 'os_version');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190321_152242_user_ip_log_update cannot be reverted.\n";

        return false;
    }
    */
}
