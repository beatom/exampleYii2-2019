<?php

use yii\db\Migration;

/**
 * Class m180710_140713_update_reviews_table
 */
class m180710_140713_update_reviews_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('manager_reviews', 'comment', $this->text()->null()->defaultValue(null));
        $this->alterColumn('manager_reviews', 'answer', $this->text()->null()->defaultValue(null));
        $this->addColumn('trading_account', 'comment', $this->text()->null()->defaultValue(null));
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
        echo "m180710_140713_update_reviews_table cannot be reverted.\n";

        return false;
    }
    */
}
