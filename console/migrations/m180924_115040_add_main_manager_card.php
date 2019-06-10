<?php

use yii\db\Migration;

/**
 * Class m180924_115040_add_main_manager_card
 */
class m180924_115040_add_main_manager_card extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('manager_card', 'is_main', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('manager_card', 'is_main');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180924_115040_add_main_manager_card cannot be reverted.\n";

        return false;
    }
    */
}
