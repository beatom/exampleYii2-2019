<?php

use yii\db\Migration;

/**
 * Class m180508_075438_add_field_to_solution
 */
class m180508_075438_add_field_to_solution extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('solution', 'partner_fee', $this->integer(11)->notNull()->defaultValue(0)->comment('Гонорар партнеру от прибыли'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('solution','partner_fee');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180508_075438_add_field_to_solution cannot be reverted.\n";

        return false;
    }
    */
}
