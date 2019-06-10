<?php

use yii\db\Migration;

/**
 * Class m190502_125640_withdraw_fee_verified
 */
class m190502_125640_withdraw_fee_verified extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_systems_withdraw', 'fee_verified', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('payment_systems_withdraw','fee_verified');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190502_125640_withdraw_fee_verified cannot be reverted.\n";

        return false;
    }
    */
}
