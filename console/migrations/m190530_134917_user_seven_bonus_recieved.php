<?php

use yii\db\Migration;

/**
 * Class m190530_134917_user_seven_bonus_recieved
 */
class m190530_134917_user_seven_bonus_recieved extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'seven_bonus_received', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user', 'seven_bonus_received');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190530_134917_user_seven_bonus_recieved cannot be reverted.\n";

        return false;
    }
    */
}
