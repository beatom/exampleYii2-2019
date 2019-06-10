<?php

use yii\db\Migration;

/**
 * Class m180608_080928_user_info
 */
class m180608_080928_user_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('user_partner_info', 'contribution_p3', $this->double()->null()->defaultValue(0));
	    $this->alterColumn('user_partner_info', 'sum_in_mount', $this->double()->null()->defaultValue(0));
	    $this->alterColumn('user_partner_info', 'sum_in_all', $this->double()->null()->defaultValue(0));
	    $this->alterColumn('user_partner_info', 'personal_contribution', $this->double()->null()->defaultValue(0));
//	    $this->alterColumn('user_partner_info', 'personal_contribution_all', $this->double()->null()->defaultValue(0));
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
        echo "m180608_080928_user_info cannot be reverted.\n";

        return false;
    }
    */
}
