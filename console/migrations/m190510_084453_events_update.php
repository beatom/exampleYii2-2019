<?php

use yii\db\Migration;

/**
 * Class m190510_084453_events_update
 */
class m190510_084453_events_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('events', 'updated_at', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('events', 'updated_at');
        return true;
    }
}
