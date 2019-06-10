<?php

use yii\db\Migration;

/**
 * Handles the creation of table `balance_log`.
 */
class m180312_142122_create_balance_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('balance_log', [
            'id' => $this->primaryKey(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'user_id' => $this->integer(11)->notNull(),
            'summ' => $this->float(11)->notNull(),
            'system' => $this->integer(11)->null(),
            'operation' => $this->integer(11)->notNull(),
            'status' => $this->integer(11)->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('balance_log');
    }
}
