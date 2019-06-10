<?php

use yii\db\Migration;

/**
 * Class m181030_104855_add_lessons_table
 */
class m181030_104855_add_lessons_table extends Migration
{
    use common\models\traits\TextTypesTrait;
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('training_lesson',[
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->null()->defaultValue(null),
            'text' => $this->longText()->null()->defaultValue(null),
            'advice' => $this->text()->null()->defaultValue(null),
            'video_url' =>  $this->string(255)->null()->defaultValue(null),
            'img' => $this->string(255)->null()->defaultValue(null),
            'show' => $this->boolean()->notNull()->defaultValue(1),
            'is_final' => $this->boolean()->notNull()->defaultValue(0),
            'question' => $this->string(255)->notNull(),
            'answer_1' => $this->string(255)->null()->defaultValue(null),
            'answer_2' => $this->string(255)->null()->defaultValue(null),
            'right_answer' => $this->integer(2)->notNull()->defaultValue(1),
            'time' => $this->string(255)->null()->defaultValue(null),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . ' COMMENT "Уроки учебного центра"');

//        $this->createTable('training_test',[
//            'id' => $this->primaryKey(),
//            'lesson_id' => $this->integer(11)->notNull(),
//            'question' => $this->string(255)->null()->defaultValue(null),
//            'show' => $this->boolean()->notNull()->defaultValue(1),
//        ], $tableOptions . ' COMMENT "Тестовые вопросы"');
//
//        $this->createTable('training_test_answer',[
//            'id' => $this->primaryKey(),
//            'test_id' => $this->integer(11)->notNull(),
//            'answer' => $this->integer(11)->notNull(),
//            'is_right' => $this->boolean()->notNull()->defaultValue(0),
//        ], $tableOptions . ' COMMENT "Ответы на тестовые вопросы"');

        $this->createTable('training_lesson_user',[
            'id' => $this->primaryKey(),
            'lesson_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
        ], $tableOptions . ' COMMENT "Пройденные уроки пользователем"');

        $this->createIndex('idx_unique_lesson_id_user_id',
            'training_lesson_user',
            ['lesson_id', 'user_id'],
            true);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_unique_lesson_id_user_id', 'training_lesson_user');
//        $this->dropTable('training_test');
        $this->dropTable('training_lesson_user');
//        $this->dropTable('training_test_answer');
        $this->dropTable('training_lesson');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181030_104855_add_lessons_table cannot be reverted.\n";

        return false;
    }
    */
}
