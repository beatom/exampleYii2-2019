<?php

use yii\db\Migration;

/**
 * Class m181216_104155_issue_108
 */
class m181216_104155_issue_108 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('manager_card', 'position', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('manager_card', 'position');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181216_104155_issue_108 cannot be reverted.\n";

        return false;
    }
    */
}
