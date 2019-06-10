<?php

use yii\db\Migration;

/**
 * Handles the creation of table `investment_daily_log`.
 */
class m180330_144439_create_investment_daily_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('investments_daily_log', [
            'id' => $this->primaryKey(),
            'investment_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'summ_start' => $this->double()->notNull()->defaultValue(0),
            'summ_add' => $this->double()->notNull()->defaultValue(0),
            'summ_end' => $this->double()->notNull()->defaultValue(0),
            'profit' => $this->double()->notNull()->defaultValue(0),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('investment_daily_log');
    }
}
