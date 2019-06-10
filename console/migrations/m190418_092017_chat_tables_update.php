<?php

use yii\db\Migration;

/**
 * Class m190418_092017_chat_tables_update
 */
class m190418_092017_chat_tables_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('moderator_rights');
        $this->dropTable('overdraft');
        $this->dropTable('notification');
        $this->dropTable('partner_balu_log');
        $this->dropTable('partner_basic_income');
        $this->dropTable('review');
        $this->dropTable('review_mark');

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('chat_message_mark', [
            'id' => $this->primaryKey(),
            'chat_message_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'like' => $this->boolean()->notNull()->defaultValue(true),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addColumn('chat_message', 'likes', $this->integer(11)->notNull()->defaultValue(0));
        $this->addColumn('chat_message', 'dislikes', $this->integer(11)->notNull()->defaultValue(0));

        $this->addColumn('user', 'chat_messages_count', $this->integer(11)->notNull()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
