<?php

use yii\db\Migration;

/**
 * Class m190530_105448_issue_193
 */
class m190530_105448_issue_193 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('user_bonus_request',[
            'id' => $this->primaryKey(),
            'user_id' =>  $this->integer(11)->notNull(),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' =>  $this->integer(3)->notNull()->defaultValue(1),
            'vk' => $this->string(255)->null()->defaultValue(null),
            'instagram' => $this->string(255)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Заявки на получение бонуса 7%"');

        $this->batchInsert('chat_template', ['id', 'synonym','text','comment','title'], [
            [
                'id' => 16,
                'synonym' => 'Сообщение при получении бонуса +7%',
                'text' => 'Поздравляем, на Ваш баланс был начислен бонус 7%',
                'comment' => '',
                'title' => 'Бонус получен',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user_bonus_request');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190530_105448_issue_193 cannot be reverted.\n";

        return false;
    }
    */
}
