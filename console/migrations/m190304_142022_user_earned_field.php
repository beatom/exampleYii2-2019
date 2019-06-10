<?php

use yii\db\Migration;

/**
 * Class m190304_142022_user_earned_field
 */
class m190304_142022_user_earned_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'earned', $this->float()->notNull()->defaultValue(0));
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'earned');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190304_142022_user_earned_field cannot be reverted.\n";

        return false;
    }
    */
}
