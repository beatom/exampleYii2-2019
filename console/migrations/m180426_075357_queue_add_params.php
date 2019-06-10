<?php

use yii\db\Migration;

/**
 * Class m180426_075357_queue_add_params
 */
class m180426_075357_queue_add_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('queue_test', 'params', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180426_075357_queue_add_params cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180426_075357_queue_add_params cannot be reverted.\n";

        return false;
    }
    */
}
