<?php

use yii\db\Migration;

/**
 * Class m190321_123613_add_avatar_to_user_objective
 */
class m190321_123613_add_avatar_to_user_objective extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_objectives', 'image', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user_objectives', 'image');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190321_123613_add_avatar_to_user_objective cannot be reverted.\n";

        return false;
    }
    */
}
