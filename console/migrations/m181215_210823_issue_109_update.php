<?php

use yii\db\Migration;

/**
 * Class m181215_210823_issue_109_update
 */
class m181215_210823_issue_109_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('visitor_log',[
            'id' => $this->primaryKey(),
            'phone' => $this->string(255)->null()->defaultValue(null),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'date_visit' => $this->string(255)->notNull(),
            'city_id' => $this->integer(5)->notNull(),
            'name' => $this->string(255)->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'sms_confirmed' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Списки временно заблокированных номеров для отправки смс"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('visitor_log');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181215_210823_issue_109_update cannot be reverted.\n";

        return false;
    }
    */
}
