<?php

use yii\db\Migration;

/**
 * Class m180409_141647_for_test
 */
class m180409_141647_for_test extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->createTable('queue_test', [
            'id' => $this->primaryKey(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_work' => $this->dateTime()->null(),
            'worked' => $this->integer(1)->notNull()->defaultValue(0)->comment('отработал'),
            'task' => $this->string()->comment('задача'),
            'type' => $this->integer(2)->defaultValue(0)->comment('может нужно разбить задачи'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180409_141647_for_test cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_141647_for_test cannot be reverted.\n";

        return false;
    }
    */
}
