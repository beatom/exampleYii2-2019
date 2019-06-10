<?php

use yii\db\Migration;

/**
 * Class m180724_111047_add_reviews_tables
 */
class m180724_111047_add_reviews_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('review', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'parent_id' => $this->integer(11)->null()->defaultValue(null),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'comment' => $this->text()->null()->defaultValue(null),
            'likes' => $this->integer(11)->notNull()->defaultValue(0),
            'dislikes' => $this->integer(11)->notNull()->defaultValue(0),
            'show' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions . ' COMMENT "Отзыв чат"');

        $this->createTable('review_mark', [
            'id' => $this->primaryKey(),
            'review_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'like' => $this->boolean()->notNull()->defaultValue(true),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable('user_ban', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'manager_id' => $this->integer(11)->notNull(),
            'permanent' => $this->boolean()->notNull()->defaultValue(0),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_end' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions . ' COMMENT "Баны пользователей"');

        $this->addColumn('user', 'user_category_id', $this->integer(11)->null()->defaultValue(null));

        $this->createTable('user_category', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'active' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions . ' COMMENT "Категории пользователей"');

        $this->createTable('user_categories_right', [
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->notNull()->unique(),
            'name' => $this->string(255)->notNull()->unsigned(),
        ], $tableOptions . ' COMMENT "Права модераторов"');

        $this->createTable('moderator_rights', [
            'id' => $this->primaryKey(),
            'user_category_id' => $this->integer(11)->notNull(),
            'user_categories_rights_id' => $this->integer(11)->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('review');
        $this->dropTable('review_mark');
        $this->dropTable('user_ban');
        $this->dropTable('user_category');
        $this->dropTable('user_categories_right');
        $this->dropTable('moderator_rights');
        $this->dropColumn('user', 'user_category_id');
        
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180724_111047_add_reviews_tables cannot be reverted.\n";

        return false;
    }
    */
}
