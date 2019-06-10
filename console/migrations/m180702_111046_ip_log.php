<?php

use yii\db\Migration;

/**
 * Class m180702_111046_ip_log
 */
class m180702_111046_ip_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('user_ip_log', 'ip', $this->string(255)->null()->defaultValue(null));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180702_111046_ip_log cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180702_111046_ip_log cannot be reverted.\n";

        return false;
    }
    */
}
