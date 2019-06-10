<?php

use yii\db\Migration;

/**
 * Class m181123_110428_manager_not_show
 */
class m181123_110428_manager_not_show extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('manager_card', 'show', $this->boolean()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('manager_card', 'show');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181123_110428_manager_not_show cannot be reverted.\n";

        return false;
    }
    */
}
