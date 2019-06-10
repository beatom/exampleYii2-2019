<?php

use yii\db\Migration;

/**
 * Class m190122_121917_issue_126_vip_users
 */
class m190122_121917_issue_126_vip_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'vip', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'vip');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190122_121917_issue_126_vip_users cannot be reverted.\n";

        return false;
    }
    */
}
