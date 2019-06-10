<?php

use yii\db\Migration;

/**
 * Class m180306_122852_create_country_tables
 */
class m180306_122852_create_country_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute(file_get_contents(__DIR__ .'/countries.sql'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('country');
        $this->dropTable('city');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180306_122852_create_country_tables cannot be reverted.\n";

        return false;
    }
    */
}
