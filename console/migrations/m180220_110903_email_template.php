<?php

use yii\db\Migration;

/**
 * Class m180220_110903_email_template
 */
class m180220_110903_email_template extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->dropTable('email_tempalte');

        $this->createTable('email_tempalte',[
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->null(),
            'title' => $this->string(255)->notNull()->comment('Тема письма'),
            'text' => $this->text()->notNull()->comment('Текст письма с тегами и переменными'),
            'comment' => $this->text()->null()->comment('подсказка при выводе'),
        ], $tableOptions . ' COMMENT "Шаблоны писем"');

        $this->insert('email_tempalte', [
            'synonym' => 'Подтверждение регистрации',
            'title' => 'Поздравляем, {{user_name}}, вы зарегистрировались на сайте invest',
            'text' => '<p>Ваш логин: {{user_name}}</p>
<p>Ваш пароль: {{password}}</p>
<p></p>
<p></p>
<p>Ваш партнер: {{partner}}</p>
    ',
            'comment' => 'Доступные переменные: {{user_name}} - ник пользователя, {{password}} - пароль, {{partner}} - ник партнера.'
        ]);

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180220_110903_email_template cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180220_110903_email_template cannot be reverted.\n";

        return false;
    }
    */
}
