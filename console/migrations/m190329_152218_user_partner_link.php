<?php

use yii\db\Migration;

/**
 * Class m190329_152218_user_partner_link
 */
class m190329_152218_user_partner_link extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'invitation_code', $this->string(100)->null()->defaultValue(null) );
        $this->addColumn('user', 'promo_code', $this->string(100)->null()->defaultValue(null) );
        $this->addColumn('user', 'promo_used', $this->boolean()->notNull()->defaultValue(false) );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'invitation_code');
        $this->dropColumn('user', 'promo_code');
        $this->dropColumn('user', 'promo_used');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190329_152218_user_partner_link cannot be reverted.\n";

        return false;
    }
    */
}
