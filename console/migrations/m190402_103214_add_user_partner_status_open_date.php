<?php

use yii\db\Migration;

/**
 * Class m190402_103214_add_user_partner_status_open_date
 */
class m190402_103214_add_user_partner_status_open_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'status_open', $this->dateTime()->null()->defaultValue(null ));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('user', 'status_open');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190402_103214_add_user_partner_status_open_date cannot be reverted.\n";

        return false;
    }
    */
}
