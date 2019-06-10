<?php

use yii\db\Migration;

/**
 * Class m190119_093406_issue_121
 */
class m190119_093406_issue_121 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('trading_account_exception',[
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'user_id' =>  $this->integer(11)->notNull(),
        ], $tableOptions . ' COMMENT "Исключение пользователей из статистикисчетов"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('trading_account_exception');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190119_093406_issue_121 cannot be reverted.\n";

        return false;
    }
    */
}
