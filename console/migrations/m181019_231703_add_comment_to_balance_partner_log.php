<?php

use yii\db\Migration;

/**
 * Class m181019_231703_add_comment_to_balance_partner_log
 */
class m181019_231703_add_comment_to_balance_partner_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_partner_log', 'comment', $this->text()->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('balance_partner_log', 'comment');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181019_231703_add_comment_to_balance_partner_log cannot be reverted.\n";

        return false;
    }
    */
}
