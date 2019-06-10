<?php

use yii\db\Migration;

/**
 * Class m180618_130319_add_message_template
 */
class m180618_130319_add_message_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'first_bonus_recived', $this->boolean()->notNull()->defaultValue(false));
        $this->batchInsert('chat_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '14',
                'synonym' => 'верификация успешно пройдена',
                'text' => 'Поздарляем! Ваш аккаунт успешно верифицирован',
                'comment' => '',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'first_bonus_recived');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180618_130319_add_message_template cannot be reverted.\n";

        return false;
    }
    */
}
