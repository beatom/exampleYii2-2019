<?php

use yii\db\Migration;

/**
 * Class m180511_120110_overdraft
 */
class m180511_120110_overdraft extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

	    $this->createTable('overdraft',[
		    'id' => $this->primaryKey(),
		    'user_id' => $this->integer(11)->notNull(),
		    'summ' => $this->integer(11)->defaultValue(null),
		    'date_open' =>  $this->date()->defaultValue(null),
		    'date_close' =>  $this->date()->defaultValue(null),
		    'is_dolg' => $this->smallInteger(1)->defaultValue(1)
	    ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('overdraft');

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180511_120110_overdraft cannot be reverted.\n";

        return false;
    }
    */
}
