<?php

use yii\db\Migration;

/**
 * Class m180912_080910_update_users_table
 */
class m180912_080910_update_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user', 'sms_code_money', $this->boolean()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('user', 'sms_code_money', $this->boolean()->notNull()->defaultValue(0));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180912_080910_update_users_table cannot be reverted.\n";

        return false;
    }
    */
}
