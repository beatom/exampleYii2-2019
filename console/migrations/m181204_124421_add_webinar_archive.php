<?php

use yii\db\Migration;

/**
 * Class m181204_124421_add_webinar_archive
 */
class m181204_124421_add_webinar_archive extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('webinar_archive',[
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->null()->defaultValue(null),
            'description' => $this->text()->null()->defaultValue(null),
            'img' => $this->string(255)->null()->defaultValue(null),
            'video_link' => $this->string(255)->null()->defaultValue(null),
            'video_duration' => $this->string(255)->null()->defaultValue(null),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'show' => $this->boolean()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Предстоящие вебинары"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('webinar_archive');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181204_124421_add_webinar_archive cannot be reverted.\n";

        return false;
    }
    */
}
