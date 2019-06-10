<?php

use yii\db\Migration;

/**
 * Class m180910_144305_add_user_ban
 */
class m180910_144305_add_user_ban extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'banned', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'banned');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180910_144305_add_user_ban cannot be reverted.\n";

        return false;
    }
    */
}
