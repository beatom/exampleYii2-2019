<?php

use yii\db\Migration;

/**
 * Class m181108_101924_popup
 */
class m181108_101924_popup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'banner_show_date', $this->dateTime()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'banner_show_date');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181108_101924_popup cannot be reverted.\n";

        return false;
    }
    */
}
