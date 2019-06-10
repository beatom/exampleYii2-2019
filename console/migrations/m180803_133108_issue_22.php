<?php

use yii\db\Migration;

/**
 * Class m180803_133108_issue_22
 */
class m180803_133108_issue_22 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('manager_reviews','traiding_period_log_id', $this->integer(11)->null()->defaultValue(null));
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
        echo "m180803_133108_issue_22 cannot be reverted.\n";

        return false;
    }
    */
}
