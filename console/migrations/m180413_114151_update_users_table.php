<?php

use yii\db\Migration;

/**
 * Class m180413_114151_update_users_table
 */
class m180413_114151_update_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'utip_login', $this->string(255)->null()->defaultValue(null)->comment('логин в терминале'));
        $this->addColumn('user', 'utip_password', $this->string(255)->null()->defaultValue(null)->comment('логин в терминале'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'utip_login');
        $this->dropColumn('user', 'utip_password');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180413_114151_update_users_table cannot be reverted.\n";

        return false;
    }
    */
}
