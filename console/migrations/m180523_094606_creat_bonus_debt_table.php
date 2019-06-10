<?php

use yii\db\Migration;

/**
 * Class m180523_094606_creat_bonus_debt_table
 */
class m180523_094606_creat_bonus_debt_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('bonus_debt', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'summ' => $this->double()->notNull()->defaultValue(0),
            'status' => $this->integer(3)->notNull()->defaultValue(1)->comment('1 - новая заявка, 2 - выполнена, 3 - отменена'),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . ' COMMENT "Бонусы которые должны быть выплачены пользователю после верификации"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('bonus_debt');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180523_094606_creat_bonus_debt_table cannot be reverted.\n";

        return false;
    }
    */
}
