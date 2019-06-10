<?php

use yii\db\Migration;

/**
 * Class m190403_234645_add_telegram_to_user_social
 */
class m190403_234645_add_telegram_to_user_social extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_social', 'telegram', $this->string(255)->null()->defaultValue(NULL));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user_social', 'telegram');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190403_234645_add_telegram_to_user_social cannot be reverted.\n";

        return false;
    }
    */
}
