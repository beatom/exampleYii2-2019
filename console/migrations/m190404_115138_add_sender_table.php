<?php

use yii\db\Migration;

/**
 * Class m190404_115138_add_sender_table
 */
class m190404_115138_add_sender_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('sender',[
            'id' => $this->primaryKey(),
            'name' =>  $this->string(255)->null()->defaultValue(null),
            'surname' =>  $this->string(255)->null()->defaultValue(null),
            'position' => $this->string(255)->null()->defaultValue(null),
            'title_en' => $this->string(255)->null()->defaultValue(null),
            'description' => $this->text()->null()->defaultValue(null),
            'description_en' => $this->text()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Отправители"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('sender');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190404_115138_add_sender_table cannot be reverted.\n";

        return false;
    }
    */
}
