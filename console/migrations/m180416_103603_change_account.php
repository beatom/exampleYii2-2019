<?php

use yii\db\Migration;

/**
 * Class m180416_103603_change_account
 */
class m180416_103603_change_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('trading_account', 'create_admin', $this->smallInteger(1)->defaultValue(0)->comment('создан через админку'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180416_103603_change_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180416_103603_change_account cannot be reverted.\n";

        return false;
    }
    */
}
