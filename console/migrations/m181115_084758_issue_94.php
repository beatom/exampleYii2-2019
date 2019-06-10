<?php

use yii\db\Migration;

/**
 * Class m181115_084758_issue_94
 */
class m181115_084758_issue_94 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('solution_reviews', [
            'id' => $this->primaryKey(),
            'solution_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'comment' => $this->string(255)->notNull(),
            'rating' => $this->double()->notNull(),
            'show' => $this->boolean()->notNull()->defaultValue(true),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions . ' COMMENT "Отзывы о готовых решениях"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('solution_reviews');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181115_084758_issue_94 cannot be reverted.\n";

        return false;
    }
    */
}
