<?php

use yii\db\Migration;

/**
 * Class m190415_130840_add_fee_to
 */
class m190415_130840_add_fee_to extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_systems', 'fee_verified', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment_systems', 'fee_verified');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190415_130840_add_fee_to cannot be reverted.\n";

        return false;
    }
    */
}
