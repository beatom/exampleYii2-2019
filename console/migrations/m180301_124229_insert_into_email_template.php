<?php

use yii\db\Migration;

/**
 * Class m180301_124229_insert_into_email_template
 */
class m180301_124229_insert_into_email_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('banner', 'subtitle', $this->string()->null()->defaultValue(NULL));

        $this->insert('email_tempalte', [
            'id' => 2,
            'synonym' => 'Уведомление о непрочитанных сообщениях',
            'title' => 'У Вас {{count}} новых сообщений на сайте invest',
            'text' => '<p>Здравствуйте: {{user_name}}</p>
                        <p>У Вас {{count}} непрочитаных сообщений</p>
                        <p></p>
                        <p>Вы можете прочесть их перейдя по ссылке {{messages_link}} </p>',
            'comment' => 'Доступные переменные: {{user_name}} - ник пользователя, {{count}} - количество сообщений, {{messages_link}} - ссылка на страницу сообщений в личном кабинете.'
        ]);
        $this->insert('email_tempalte', [
            'id' => 3,
            'synonym' => 'Уведомление о новых сообщениях от пользователя',
            'title' => 'У Вас {{count}} новых сообщений на сайте invest',
            'text' => '<p>Здравствуйте: {{user_name}}</p>
                        <p>У Вас {{count}} непрочитаных сообщений от пользователя {{sender_name}}</p>
                        <p></p>
                        <p>Вы можете прочесть их перейдя по ссылке {{messages_link}} </p>',
            'comment' => 'Доступные переменные: {{user_name}} - ник пользователя, {{count}} - количество сообщений, {{messages_link}} - ссылка на страницу сообщений в личном кабинете, {{sender_name}}.'
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('banner', 'subtitle');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180301_124229_insert_into_email_template cannot be reverted.\n";

        return false;
    }
    */
}
