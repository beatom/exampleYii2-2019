<?php

use yii\db\Migration;

/**
 * Class m190313_140727_admin_user_ip_log
 */
class m190313_140727_admin_user_ip_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('user_ip_log_admin',[
            'id' => $this->primaryKey(),
            'user_id' =>  $this->integer(11)->notNull(),
            'ip' =>  $this->string(255)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'session_id' => $this->string(255)->notNull(),
        ], $tableOptions . ' COMMENT "Ip пользователей которые сейчас в админке"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_ip_log_admin');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190313_140727_admin_user_ip_log cannot be reverted.\n";

        return false;
    }
    */
}
