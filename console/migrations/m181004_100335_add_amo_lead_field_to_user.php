<?php

use yii\db\Migration;

/**
 * Class m181004_100335_add_amo_lead_field_to_user
 */
class m181004_100335_add_amo_lead_field_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'amo_contact_stage', $this->integer(4)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'amo_contact_stage');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181004_100335_add_amo_lead_field_to_user cannot be reverted.\n";

        return false;
    }
    */
}
