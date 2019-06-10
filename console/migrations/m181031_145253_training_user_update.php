<?php

use yii\db\Migration;

/**
 * Class m181031_145253_training_user_update
 */
class m181031_145253_training_user_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'training_complete', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('user', 'unlogin', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'training_complete');
        $this->dropColumn('user', 'unlogin');
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181031_145253_training_user_update cannot be reverted.\n";

        return false;
    }
    */
}
