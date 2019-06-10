<?php

use yii\db\Migration;

/**
 * Class m180319_131328_add_email_template
 */
class m180319_131328_add_email_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('email_tempalte', [
            'id' => 6,
            'synonym' => 'Подтверждение email',
            'title' => 'Подтверждение email на сайте invest24.com',
            'text' => '<p>Для подтверждения email перейдите по ссылке: {{link}}</p>',
            'comment' => 'Доступные переменные: {{link}} - ссылка для подтверждения электронной почты'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180319_131328_add_email_template cannot be reverted.\n";

        return false;
    }
    */
}
