<?php

use yii\db\Migration;

/**
 * Class m180512_075004_add_offer_to_solution
 */
class m180512_075004_add_offer_to_solution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('solution', 'manager_fee', $this->integer(11)->notNull()->defaultValue(0)->comment('Гонорар управляющим'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('solution', 'manager_fee');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180512_075004_add_offer_to_solution cannot be reverted.\n";

        return false;
    }
    */
}
