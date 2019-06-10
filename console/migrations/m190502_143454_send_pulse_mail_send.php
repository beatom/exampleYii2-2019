<?php

use yii\db\Migration;

/**
 * Class m190502_143454_send_pulse_mail_send
 */
class m190502_143454_send_pulse_mail_send extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('sendpulse_notifications',[
            'id' => $this->primaryKey(),
            'user_id' =>  $this->string(255)->null()->defaultValue(null),
            'notification' =>  $this->string(255)->null()->defaultValue(null),
            'data' => $this->text()->null()->defaultValue(null),
            'date_add' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . ' COMMENT "Лог отправленных уведомлений SendPulse"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('sendpulse_notifications');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190502_143454_send_pulse_mail_send cannot be reverted.\n";

        return false;
    }
    */
}
