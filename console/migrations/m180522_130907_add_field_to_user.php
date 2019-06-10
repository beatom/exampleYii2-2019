<?php

use yii\db\Migration;

/**
 * Class m180522_130907_add_field_to_user
 */
class m180522_130907_add_field_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'utip_email', $this->string(255)->null()->defaultValue('null')->after('utip_login'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'utip_email');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180522_130907_add_field_to_user cannot be reverted.\n";

        return false;
    }
    */
}
