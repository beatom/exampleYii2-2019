<?php

use yii\db\Migration;

/**
 * Class m190208_091847_issue132
 */
class m190208_091847_issue132 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'restart_date', $this->dateTime()->null()->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'restart_date');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190208_091847_issue132 cannot be reverted.\n";

        return false;
    }
    */
}
