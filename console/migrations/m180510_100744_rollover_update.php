<?php

use yii\db\Migration;

/**
 * Class m180510_100744_rollover_update
 */
class m180510_100744_rollover_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->dropTable('message_tempalte');
        $this->dropTable('message');

        $this->createTable('trading_account_change_history',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'old_account_number' => $this->string(255)->notNull(),
            'old_password' => $this->string(255)->notNull(),
            'old_utip_account_id' => $this->integer(11)->notNull(),
            'old_investor_password' => $this->string(255)->notNull(),
            'new_account_number' => $this->string(255)->notNull(),
            'new_password' => $this->string(255)->notNull(),
            'new_utip_account_id' => $this->integer(11)->notNull(),
            'new_investor_password' => $this->string(255)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('message_tempalte',[
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->null(),
            'text' => $this->text()->notNull()->comment('Текст c переменными'),
        ], $tableOptions);

        $this->createTable('message',[
            'id' => $this->primaryKey(),
            'user_id_from' => $this->integer()->notNull(),
            'user_id_to' => $this->integer()->notNull(),
            'title' => $this->string(255)->null(),
            'message' => $this->text()->notNull(),
            'date_add' => $this->dateTime(),
        ], $tableOptions);




        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180510_100744_rollover_update cannot be reverted.\n";

        return false;
    }
    */
}
