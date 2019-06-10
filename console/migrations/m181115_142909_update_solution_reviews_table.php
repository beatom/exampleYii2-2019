<?php

use yii\db\Migration;

/**
 * Class m181115_142909_update_solution_reviews_table
 */
class m181115_142909_update_solution_reviews_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('solution_reviews', 'comment', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181115_142909_update_solution_reviews_table cannot be reverted.\n";

        return false;
    }
    */
}
