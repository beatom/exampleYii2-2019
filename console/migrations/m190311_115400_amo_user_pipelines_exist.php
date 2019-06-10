<?php

use yii\db\Migration;

/**
 * Class m190311_115400_amo_user_pipelines_exist
 */
class m190311_115400_amo_user_pipelines_exist extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('amo_user_pipelines',[
            'id' => $this->primaryKey(),
            'user_id' =>  $this->integer(11)->notNull(),
            'synergy_1' =>  $this->string(50)->null()->defaultValue(null),
            'meet_up_moscow' => $this->string(50)->null()->defaultValue(null),
            'save_capital' => $this->string(50)->null()->defaultValue(null),
            'meaningful_customer_card' => $this->string(50)->null()->defaultValue(null),
            'plus_50' => $this->string(50)->null()->defaultValue(null),
            'loyalty_program' => $this->string(50)->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Отметки созданных сделок по пользователям в определенных воронках"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('amo_user_pipelines');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190311_115400_amo_user_pipelines_exist cannot be reverted.\n";

        return false;
    }
    */
}
