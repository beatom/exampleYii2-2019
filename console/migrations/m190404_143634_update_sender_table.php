<?php

use yii\db\Migration;

/**
 * Class m190404_143634_update_sender_table
 */
class m190404_143634_update_sender_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('sender', 'title_en');
        $this->dropColumn('sender', 'description');
        $this->dropColumn('sender', 'description_en');
        $this->addColumn('sender', 'avatar', $this->string(255)->null()->defaultValue(null));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('user_message',[
            'id' => $this->primaryKey(),
            'sender_id' =>  $this->integer(11)->notNull(),
            'user_id' =>  $this->integer(11)->notNull(),
            'status' =>  $this->boolean()->notNull()->defaultValue(false),
            'title' => $this->string(255)->null()->defaultValue(null),
            'text' => $this->text()->null()->defaultValue(null),
            'date_add' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_delete' => $this->dateTime()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Сообщения пользователям"');

        $this->batchInsert('options', [ 'key', 'description'], [
            [
                'key' => 'bets_sender_id',
                'description' => 'Отображаемый отправитель на странице ставок в ЛК',
            ],
            [
                'key' => 'bets_sender_message',
                'description' => 'Сообщение администратора на странице ставок в ЛК',
            ],
        ]);

       // $this->addColumn('chat_template', 'title', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_message');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190404_143634_update_sender_table cannot be reverted.\n";

        return false;
    }
    */
}
