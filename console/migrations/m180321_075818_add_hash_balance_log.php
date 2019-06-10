<?php

use yii\db\Migration;

/**
 * Class m180321_075818_add_hash_balance_log
 */
class m180321_075818_add_hash_balance_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_log', 'hash_payment', $this->string()->null()->defaultValue(null)->comment('хеш от платежной системы'));
        $this->alterColumn('balance_log', 'system', $this->integer(11)->notNull()->defaultValue(0)->comment('id платежной системы, константы в классе'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('balance_log', 'hash_payment');
        return true;
    }
}
