<?php

use yii\db\Migration;

/**
 * Class m190319_144923_update_input_table
 */
class m190319_144923_update_input_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('news', 'from', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('news', 'from_en', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('news', 'cat', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('news', 'cat_en', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('news', 'from');
        $this->addColumn('news', 'from_en');
        $this->addColumn('news', 'cat');
        $this->addColumn('news', 'cat_en');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190319_144923_update_input_table cannot be reverted.\n";

        return false;
    }
    */
}
