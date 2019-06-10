<?php

use yii\db\Migration;

/**
 * Class m190412_100253_user_news_read
 */
class m190412_100253_user_news_read extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('news_user_red',[
            'id' => $this->primaryKey(),
            'user_id' =>  $this->string(255)->null()->defaultValue(null),
            'news_id' =>  $this->string(255)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Отметки прочитанных новостей"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('news_user_red');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190412_100253_user_news_read cannot be reverted.\n";

        return false;
    }
    */
}
