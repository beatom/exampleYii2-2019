<?php

use yii\db\Migration;

/**
 * Class m180430_095409_update_request_table
 */
class m180430_095409_update_request_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('request_change_leverage','user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180430_095409_update_request_table cannot be reverted.\n";

        return false;
    }
    */
}
