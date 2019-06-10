<?php

use yii\db\Migration;

/**
 * Class m190513_080200_withdraw_min_max
 */
class m190513_080200_withdraw_min_max extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_systems_withdraw', 'sum_min', $this->integer(11)->notNull()->defaultValue(5));
        $this->addColumn('payment_systems_withdraw', 'sum_max', $this->integer(11)->notNull()->defaultValue(10000));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('payment_systems_withdraw', 'sum_min');
        $this->dropColumn('payment_systems_withdraw', 'sum_max');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190513_080200_withdraw_min_max cannot be reverted.\n";

        return false;
    }
    */
}
