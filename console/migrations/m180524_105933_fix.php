<?php

use yii\db\Migration;

/**
 * Class m180524_105933_fix
 */
class m180524_105933_fix extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

	    $this->dropTable('partner_basic_income');

	    $this->createTable('partner_basic_income',[
		    'id' => $this->primaryKey(),
		    'user_id_from' => $this->integer(11)->notNull()->unsigned(),
		    'user_id_to' => $this->integer(11)->notNull()->unsigned(),
		    'summ' => $this->double()->null()->defaultValue(0),
		    'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
		    'description' => $this->string()->null()->defaultValue(null),
	    ], $tableOptions . ' COMMENT "Основной доход, Привлечение трейдеров"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180524_105933_fix cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180524_105933_fix cannot be reverted.\n";

        return false;
    }
    */
}
