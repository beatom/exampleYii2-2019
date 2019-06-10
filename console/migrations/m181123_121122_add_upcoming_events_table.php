<?php

use yii\db\Migration;

/**
 * Class m181123_121122_add_upcoming_events_table
 */
class m181123_121122_add_upcoming_events_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('webinar',[
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->null()->defaultValue(null),
            'title_en' => $this->string(255)->null()->defaultValue(null),
            'description' => $this->text()->null()->defaultValue(null),
            'description_en' => $this->text()->null()->defaultValue(null),
            'img' => $this->string(255)->null()->defaultValue(null),
            'img_en' => $this->string(255)->null()->defaultValue(null),
            'button_text' => $this->string(255)->null()->defaultValue(null),
            'button_text_en' => $this->string(255)->null()->defaultValue(null),
            'button_link' => $this->string(255)->null()->defaultValue(null),

            'date_end' => $this->dateTime()->defaultValue(null),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'show' => $this->boolean()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Предстоящие вебинары"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropTable('webinar');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181123_121122_add_upcoming_events_table cannot be reverted.\n";

        return false;
    }
    */
}
