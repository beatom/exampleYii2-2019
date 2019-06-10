<?php

use yii\db\Migration;

/**
 * Class m180523_103645_basic_in_come
 */
class m180523_103645_basic_in_come extends Migration
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
		    'summ' => $this->integer(11)->null()->defaultValue(0),
		    'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
		    'description' => $this->string()->null()->defaultValue(null),
	    ], $tableOptions . ' COMMENT "Основной доход, Привлечение трейдеров"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180523_103645_basic_in_come cannot be reverted.\n";

        return false;
    }
    */
}
