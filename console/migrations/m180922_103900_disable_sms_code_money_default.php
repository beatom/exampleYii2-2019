<?php

use yii\db\Migration;

/**
 * Class m180922_103900_disable_sms_code_money_default
 */
class m180922_103900_disable_sms_code_money_default extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->alterColumn('user', 'sms_code_money', $this->boolean()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('user', 'sms_code_money', $this->boolean()->notNull()->defaultValue(1));

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180922_103900_disable_sms_code_money_default cannot be reverted.\n";

        return false;
    }
    */
}
