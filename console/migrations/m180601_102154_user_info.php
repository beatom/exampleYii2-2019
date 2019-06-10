<?php

use yii\db\Migration;

/**
 * Class m180601_102154_user_info
 */
class m180601_102154_user_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('user_partner_info', 'personal_contribution_all', $this->integer(11)->null()->defaultValue(0)->comment('средства прошли через Платежки')->after('personal_contribution'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180601_102154_user_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180601_102154_user_info cannot be reverted.\n";

        return false;
    }
    */
}
