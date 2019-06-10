<?php

use yii\db\Migration;

/**
 * Class m180205_135714_social_users_table
 */
class m180205_135714_social_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        //соц сети
        $this->createTable('user_social',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->unique()->unsigned(),
            'facebook' => $this->string(255)->null()->defaultValue(NULL),
            'vk' => $this->string(255)->null()->defaultValue(NULL),
            'instagram' => $this->string(255)->null()->defaultValue(NULL),
            'skype' => $this->string(255)->null()->defaultValue(NULL),
            'whatsapp' => $this->string(255)->null()->defaultValue(NULL),
            'twitter' => $this->string(255)->null()->defaultValue(NULL),
        ], $tableOptions . ' COMMENT "аккаунты пользователей"');

        //seo
        $this->createTable('seo',[
            'id' => $this->primaryKey(),
            'meta_title' => $this->string(255)->null()->defaultValue(NULL),
            'meta_description' => $this->string(255)->null()->defaultValue(NULL),
            'meta_keyword' => $this->string(255)->null()->defaultValue(NULL),
            'url' => $this->string(255)->null()->defaultValue(NULL),
            'content' => $this->text()->null()->defaultValue(NULL),
        ], $tableOptions . ' COMMENT "seo данные"');

        //банер
        $this->createTable('main_banner',[
            'id' => $this->primaryKey(),
            'img' => $this->string(255)->null()->defaultValue(NULL)->comment('url к кортинке'),
            'text_babanner' => $this->string(255)->null()->defaultValue(NULL)->comment('title banner'),
            'text_buton' => $this->text()->null()->defaultValue(NULL)->comment('основной текст банера'),
            'url' => $this->string(255)->null()->defaultValue(NULL)->comment('ссылка'),
            'position' => $this->integer(1)->null()->defaultValue(1)->comment('место размещения'),
        ], $tableOptions . ' COMMENT "баннера"');

        $this->createTable('sms_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'phone' => $this->string(50)->null()->defaultValue(NULL),
            'text' => $this->text()->null()->defaultValue(NULL),
            'date_add' => $this->dateTime(),
            'status' => $this->text()->null()->defaultValue(NULL)->comment('ответ от сендера'),
        ], $tableOptions . ' COMMENT "СМС лог"');

        $this->createTable('user_ip_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'ip' => $this->string(25)->null()->defaultValue(NULL),
            'date_add' => $this->dateTime(),
        ], $tableOptions );

        //документы пользователя
        $this->createTable('user_document',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'pasport_1' => $this->string(255)->null()->defaultValue(NULL)->comment('путь к фото страницы паспорта 1'),
            'pasport_2' => $this->string(255)->null()->defaultValue(NULL)->comment('путь к фото страницы паспорта 2'),
        ], $tableOptions . ' COMMENT "Документы пользователей"');

        $this->createIndex('user_doc-user-id', 'user_document', 'user_id');

        //шаблоны писем
        $this->createTable('email_tempalte',[
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->null(),
            'title' => $this->string(255)->notNull()->comment('Тема письма'),
            'text' => $this->text()->notNull()->comment('Текст письма с тегами и переменными'),
        ], $tableOptions . ' COMMENT "Шаблоны писем"');

        //шаблоны sms
        $this->createTable('sms_template',[
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->null(),
            'text' => $this->text()->notNull()->comment('Текст c переменными'),
        ], $tableOptions . ' COMMENT "Шаблоны SMS"');

        $this->createTable('message_tempalte',[
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->null(),
            'text' => $this->text()->notNull()->comment('Текст c переменными'),
        ], $tableOptions);

        $this->createTable('message',[
            'id' => $this->primaryKey(),
            'user_id_from' => $this->integer()->notNull(),
            'user_id_to' => $this->integer()->notNull(),
            'title' => $this->string(255)->null(),
            'message' => $this->text()->notNull(),
            'date_add' => $this->dateTime(),
        ], $tableOptions);

        //таблица опций
        $this->createTable('options',[
            'id' => $this->primaryKey(),
            'key' => $this->string(255)->null(),
            'value' => $this->text()->null()->defaultValue(NULL),
            'description' => $this->string()->null()->defaultValue(NULL)->comment('Описание параметра'),
            'section' => $this->string()->null()->defaultValue(NULL)->comment('раздел настроек, например калькулятор облигаций'),
        ], $tableOptions . ' COMMENT "таблица опций"');

        //таблица новостей
        $this->createTable('news',[
            'id' => $this->primaryKey(),
            'date_add' => $this->dateTime()->null(),
            'img' => $this->string()->null()->defaultValue(NULL)->comment('путь к картинке'),
            'title' => $this->string()->notNull(),
            'text_small' => $this->text()->null()->defaultValue(NULL)->comment('краткое описание новости'),
            'text_big' => $this->text()->null()->defaultValue(NULL)->comment('Полная новость'),
            'synonym' => $this->string()->null()->defaultValue(NULL)->comment('ЧПУ'),
            'meta_title' => $this->string()->null()->defaultValue(NULL),
            'meta_description' => $this->string()->null()->defaultValue(NULL),
            'meta_keyword' => $this->string()->null()->defaultValue(NULL),
            'status' => $this->smallInteger(1)->null()->defaultValue(1)->comment('enable - 1 disable - 0'),
        ], $tableOptions . ' COMMENT "таблица новостей"');

        //таблица акций
        $this->createTable('shares',[
            'id' => $this->primaryKey(),
            'date_start' => $this->date()->null(),
            'date_end' => $this->date()->null(),
            'img' => $this->string()->null()->defaultValue(NULL)->comment('путь к картинке'),
            'title' => $this->string()->notNull(),
            'text_small' => $this->text()->null()->defaultValue(NULL)->comment('краткое описание новости'),
            'text_big' => $this->text()->null()->defaultValue(NULL)->comment('Полная новость'),
            'synonym' => $this->string()->null()->defaultValue(NULL)->comment('ЧПУ'),
            'meta_title' => $this->string()->null()->defaultValue(NULL),
            'meta_description' => $this->string()->null()->defaultValue(NULL),
            'meta_keyword' => $this->string()->null()->defaultValue(NULL),
            'status' => $this->smallInteger(1)->null()->defaultValue(1)->comment('enable - 1 disable - 0'),
        ], $tableOptions . ' COMMENT "таблица акций"');

        //таблица логов подтверждения
        $this->createTable('log_confirm',[
            'id' => $this->primaryKey(),
            'date_add' => $this->integer()->null(),
            'phone' => $this->string()->null()->defaultValue(NULL),
            'email' => $this->string()->null()->defaultValue(NULL),
            'code' => $this->string()->null()->defaultValue(NULL)->comment('отправленый код'),
        ], $tableOptions . ' COMMENT "таблица логов кодов подтверждений"');

        //таблица баннеров
        $this->createTable('banner',[
            'id' => $this->primaryKey(),
            'img' => $this->string()->null()->defaultValue(NULL),
            'text_banner' => $this->string()->null()->defaultValue(NULL),
            'text_button' => $this->string()->null()->defaultValue(NULL),
            'url' => $this->string()->null()->defaultValue(NULL)->comment('ссылка перехода'),
            'position' => $this->integer()->notNull()->defaultValue(1),
            'status' => $this->integer()->notNull()->defaultValue(1),
        ], $tableOptions . ' COMMENT "таблица логов кодов подтверждений"');



    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('user_social');
        $this->dropTable('seo');
        $this->dropTable('main_banner');
        $this->dropTable('sms_log');
        $this->dropTable('user_ip_log');
        $this->dropTable('user_document');
        $this->dropTable('email_tempalte');
        $this->dropTable('sms_template');
        $this->dropTable('message_tempalte');
        $this->dropTable('message');
        $this->dropTable('options');
        $this->dropTable('news');
        $this->dropTable('shares');
        $this->dropTable('log_confirm');
        $this->dropTable('banner');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180205_135714_social_users_table cannot be reverted.\n";

        return false;
    }
    */
}
