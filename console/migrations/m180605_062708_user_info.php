<?php

use yii\db\Migration;

/**
 * Class m180605_062708_user_info
 */
class m180605_062708_user_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('user_partner_info', 'contribution_p3', $this->integer(11)->null()->defaultValue(0)->comment('партнерка пункт 3')->after('personal_contribution'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user_partner_info', 'contribution_p3');

        return true;
    }
}
