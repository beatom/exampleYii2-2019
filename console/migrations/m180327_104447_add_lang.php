<?php

use yii\db\Migration;

/**
 * Class m180327_104447_add_lang
 */
class m180327_104447_add_lang extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('page','title_en', $this->string()->defaultValue(null)->after('title'));
        $this->addColumn('page','content_en', $this->text()->defaultValue(null)->after('content'));
        $this->addColumn('page','meta_title_en', $this->string()->defaultValue(null)->after('meta_title'));
        $this->addColumn('page','meta_description_en', $this->string()->defaultValue(null)->after('meta_description'));
        $this->addColumn('page','meta_keyword_en', $this->string()->defaultValue(null)->after('meta_keyword'));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('user_partner_info',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->unique()->unsigned(),
            'sum_in_mount' => $this->integer(11)->notNull()->defaultValue(0)->comment('сумма привлеченных за текущий месяц'),
            'sum_in_all' => $this->integer(11)->notNull()->defaultValue(0)->comment('сумма привлеченных за все время'),
        ], $tableOptions . ' COMMENT "Информация о партнерке, чтоб не считать на лету"');

        $this->addColumn('user_partner_info', 'personal_contribution', $this->integer(11)->defaultValue(0)->comment('внесенные личные средства'));
        $this->addColumn('user_partner_info', 'count_lower_partners', $this->integer(11)->defaultValue(0)->comment('количество партнеров со статусом меньше на один'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        echo 'not delete migrate m180327_104447_add_lang';
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180327_104447_add_lang cannot be reverted.\n";

        return false;
    }
    */
}
