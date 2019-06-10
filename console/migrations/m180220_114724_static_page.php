<?php

use yii\db\Migration;

/**
 * Class m180220_114724_static_page
 */
class m180220_114724_static_page extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('page',[
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->notNull(),
            'content' => $this->text()->null()->defaultValue(NULL),
            'meta_title' => $this->string(255)->null()->defaultValue(NULL),
            'meta_description' => $this->string(255)->null()->defaultValue(NULL),
            'meta_keyword' => $this->string(255)->null()->defaultValue(NULL),
            'url' => $this->string(255)->null()->defaultValue(NULL),
            'date_add' => $this->dateTime()->null(),
            'status' => $this->integer(1)->null()->defaultValue(0)->comment('1-enable, 0-disable'),
        ], $tableOptions . ' COMMENT "статистические страницы"');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('page');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180220_114724_static_page cannot be reverted.\n";

        return false;
    }
    */
}
