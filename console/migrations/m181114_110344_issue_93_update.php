<?php

use yii\db\Migration;

/**
 * Class m181114_110344_issue_93_update
 */
class m181114_110344_issue_93_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'amo_tag_level', $this->integer(3)->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'amo_tag_level');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181114_110344_issue_93_update cannot be reverted.\n";

        return false;
    }
    */
}
