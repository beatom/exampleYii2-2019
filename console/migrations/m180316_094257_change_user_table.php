<?php

use yii\db\Migration;

/**
 * Class m180316_094257_change_user_table
 */
class m180316_094257_change_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user', 'city_id');
        $this->addColumn('user', 'city_name', $this->string(255)->null()->defaultValue(NULL)->after('country_id'));
        $this->dropTable('city');
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
        echo "m180316_094257_change_user_table cannot be reverted.\n";

        return false;
    }
    */
}
