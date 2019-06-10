<?php

use yii\db\Migration;

/**
 * Class m180405_130248_partner_balance
 */
class m180405_130248_partner_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_partner_log', 'from_user_id', $this->integer(11)->null()->defaultValue(null)->comment('от кого'));
        $this->addColumn('user_partner_info', 'arr_line', $this->text()->defaultValue(null)->comment('партнеры по линиям'));
        $this->addColumn('user_partner_info', 'all_partners', $this->text()->defaultValue(null)->comment(''));
        $this->addColumn('user_partner_info', 'piramida', $this->text()->defaultValue(null)->comment('пирамида'));
        $this->addColumn('user_partner_info', 'deposid_id', $this->integer(11)->defaultValue(0)->comment('id начисленный, програма депозит'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180405_130248_partner_balance cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180405_130248_partner_balance cannot be reverted.\n";

        return false;
    }
    */
}
