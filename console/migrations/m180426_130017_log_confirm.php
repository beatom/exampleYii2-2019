<?php

use yii\db\Migration;

/**
 * Class m180426_130017_log_confirm
 */
class m180426_130017_log_confirm extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('log_confirm', 'user_id', $this->integer(11)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180426_130017_log_confirm cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180426_130017_log_confirm cannot be reverted.\n";

        return false;
    }
    */
}
