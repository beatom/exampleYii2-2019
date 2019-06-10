<?php

use yii\db\Migration;

/**
 * Class m180706_093730_add_date_to_rewies
 */
class m180706_093730_add_date_to_rewies extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('manager_reviews','date_add',$this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('manager_reviews','date_add');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180706_093730_add_date_to_rewies cannot be reverted.\n";

        return false;
    }
    */
}
