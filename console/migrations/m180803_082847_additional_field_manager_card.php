<?php

use yii\db\Migration;

/**
 * Class m180803_082847_additional_field_manager_card
 */
class m180803_082847_additional_field_manager_card extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('manager_card', 'description', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('manager_card', 'description');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180803_082847_additional_field_manager_card cannot be reverted.\n";

        return false;
    }
    */
}
