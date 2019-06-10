<?php

use yii\db\Migration;

/**
 * Class m180503_144956_update_solutions_table
 */
class m180503_144956_update_solutions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('solution', 'is_active', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('solution', 'is_active');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180503_144956_update_solutions_table cannot be reverted.\n";

        return false;
    }
    */
}
