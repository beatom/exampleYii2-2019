<?php

use yii\db\Migration;

/**
 * Class m180531_090200_user_capital_defence
 */
class m180531_090200_user_capital_defence extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('investment_protection', [
            'id' => $this->primaryKey(),
            'investment_id' => $this->integer(11)->null(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_update' => $this->dateTime()->null()->defaultValue(null),
            'percent' => $this->integer(3)->notNull()->defaultValue(50),
        ], $tableOptions . ' COMMENT "Процент зашиты капитала пользователя"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('investment_protection');
        return true;
    }
    
}
