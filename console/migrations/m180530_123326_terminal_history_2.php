<?php

use yii\db\Migration;

/**
 * Class m180530_123326_terminal_history_2
 */
class m180530_123326_terminal_history_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {


	    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
	    $this->createTable('trading_account_history_terminal_2', [
		    'ID' => $this->integer(11)->null(),
		    'id_terminal' => $this->integer(11)->null(),
		    'id_trading' => $this->integer(11)->null(),
		    'VOLUME' => $this->string(255)->null(),
		    'CLOSE_DATE' => $this->integer(11)->null(),
		    'OPEN_DATE' => $this->integer(11)->null(),
		    'OPEN_PRICE' => $this->float()->null(),
		    'CLOSE_PRICE' => $this->float()->null(),
		    'POSITION_TYPE' => $this->string(255)->null(),
		    'SWAP' => $this->float()->null(),
		    'PROFIT' => $this->float()->null(),
		    'SYMBOL' => $this->string(255)->null(),
		    'SL' => $this->float()->null(),
		    'TP' => $this->float()->null(),
	    ], $tableOptions . ' COMMENT "История сделок по аккаунту из терминала2"');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('trading_account_history_terminal_2');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180530_123326_terminal_history_2 cannot be reverted.\n";

        return false;
    }
    */
}
