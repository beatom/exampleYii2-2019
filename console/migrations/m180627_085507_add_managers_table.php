<?php

use yii\db\Migration;

/**
 * Class m180627_085507_add_managers_table
 */
class m180627_085507_add_managers_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('manager_card', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->null()->defaultValue(null),
            'phone' => $this->string(255)->null()->defaultValue(null),
            'clear_phone' => $this->string(255)->null()->defaultValue(null),
            'email' => $this->string(255)->null()->defaultValue(null),
            'avatar' => $this->string(255)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Карточки менеджеров в кабинете"');

        $this->addColumn('user', 'manager_card_id', $this->integer(11)->null()->defaultValue(null));


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('manager_card');
        $this->dropColumn('user', 'manager_card_id');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180627_085507_add_managers_table cannot be reverted.\n";

        return false;
    }
    */
}
