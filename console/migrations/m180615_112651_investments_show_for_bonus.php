<?php

use yii\db\Migration;

/**
 * Class m180615_112651_investments_show_for_bonus
 */
class m180615_112651_investments_show_for_bonus extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'show_till_verification', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'show_till_verification');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180615_112651_investments_show_for_bonus cannot be reverted.\n";

        return false;
    }
    */
}
