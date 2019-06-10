<?php

use yii\db\Migration;

/**
 * Class m180512_081040_add_chat_template
 */
class m180512_081040_add_chat_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->batchInsert('chat_template', [ 'id', 'synonym', 'text', 'comment'], [
		    [
			    'id' => '9',
			    'synonym' => 'закрытие Овердрафта',
			    'text' => 'закрытие Овердрафта  {{amount}} {{date_close}}.',
			    'comment' => 'Доступные переменные: {{amount}} - сумма , {{date_close}} - дата закрытия',
		    ],
		    [
			    'id' => '10',
			    'synonym' => 'открытие Овердрафта',
			    'text' => 'открытие Овердрафта в размере {{amount}} {{date_close}}.',
			    'comment' => 'Доступные переменные: {{amount}} - сумма , {{date_close}} - дата закрытия',
		    ],
		    [
			    'id' => '11',
			    'synonym' => 'неделя до закрытия Овердрафта',
			    'text' => 'неделя до закрытия Овердрафта в размере {{amount}} {{date_close}}.',
			    'comment' => 'Доступные переменные: {{amount}} - сумма , {{date_close}} - дата закрытия',
		    ],
	    ]);
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
        echo "m180512_081040_add_chat_template cannot be reverted.\n";

        return false;
    }
    */
}
