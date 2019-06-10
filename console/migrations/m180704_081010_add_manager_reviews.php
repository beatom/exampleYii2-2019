<?php

use yii\db\Migration;

/**
 * Class m180704_081010_add_manager_reviews
 */
class m180704_081010_add_manager_reviews extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('manager_reviews', [
            'id' => $this->primaryKey(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'traiding_period_log_id' => $this->integer(11)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'comment' => $this->string(255)->notNull(),
            'rating' => $this->double()->notNull(),
            'answer' => $this->string(255)->null()->defaultValue(null),
            'show' => $this->boolean()->notNull()->defaultValue(true),
        ], $tableOptions . ' COMMENT "Карточки менеджеров в кабинете"');

        $this->addColumn('traiding_period_log', 'rolloved', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('traiding_period_log', 'manager_comment', $this->string(255)->null()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('manager_reviews');
        $this->dropColumn('traiding_period_log', 'rolloved');
        $this->dropColumn('traiding_period_log', 'manager_comment');
        return true;
    }
    
}
