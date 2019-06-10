<?php

use yii\db\Migration;

/**
 * Class m190307_124913_user_amo_name_level
 */
class m190307_124913_user_amo_name_level extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'amo_name_level', $this->integer(3)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'amo_name_level');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190307_124913_user_amo_name_level cannot be reverted.\n";

        return false;
    }
    */
}
