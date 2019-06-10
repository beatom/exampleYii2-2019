<?php

use yii\db\Migration;

/**
 * Class m180402_101801_add_email_shablon
 */
class m180402_101801_add_email_shablon extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('email_tempalte', [
            'id' => 11,
            'synonym' => 'Начисление бонусов',
            'title' => 'Вам начислены бонусы на сайте invest',
            'text' => '<p>Здравствуйте: {{user_name}}</p>
                        <p>Вам начислено {{summ}} бонусных $</p>
                        <p>{{description}} </p>',
            'comment' => 'Доступные переменные: {{user_name}} - ник пользователя, {{summ}} - бонусные долары, {{description}} - описание в БД.'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180402_101801_add_email_shablon cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180402_101801_add_email_shablon cannot be reverted.\n";

        return false;
    }
    */
}
