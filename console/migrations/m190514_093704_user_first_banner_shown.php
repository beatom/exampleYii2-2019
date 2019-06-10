<?php

use yii\db\Migration;

/**
 * Class m190514_093704_user_first_banner_shown
 */
class m190514_093704_user_first_banner_shown extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'first_banner_shown', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'first_banner_shown');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190514_093704_user_first_banner_shown cannot be reverted.\n";

        return false;
    }
    */
}
