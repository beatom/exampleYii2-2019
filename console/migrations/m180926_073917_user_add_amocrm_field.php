<?php

use yii\db\Migration;

/**
 * Class m180926_073917_user_add_amocrm_field
 */
class m180926_073917_user_add_amocrm_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'amo_contact_id', $this->integer(11)->null()->defaultValue(null));
        $this->addColumn('manager_card', 'amo_user_id', $this->integer(11)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'amo_contact_id');
        $this->dropColumn('manager_card', 'amo_user_id');
        return true;
    }

}
