<?php

use yii\db\Migration;

/**
 * Class m180730_104407_add_option_dublicate_terminal_history
 */
class m180730_104407_add_option_dublicate_terminal_history extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'terminal_history_duplicate_1',
                'value' => 0,
                'description' => 'Найдены дубликаты в истории терминала',
            ],
            [
                'key' => 'terminal_history_duplicate_2',
                'value' => 0,
                'description' => 'Найдены дубликаты в истории терминала отображаемой пользователям',
            ],
            [
                'key' => 'terminal_duplicate_search',
                'value' => 'SELECT id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP, COUNT(*) AS duplicates
                            FROM trading_account_history_terminal_2
                            GROUP BY id_terminal,  id_trading, VOLUME, CLOSE_DATE, OPEN_DATE, OPEN_PRICE, CLOSE_PRICE, POSITION_TYPE, SWAP, PROFIT, SYMBOL, SL, TP
                            HAVING duplicates > 1',
                'description' => 'Код для поиска дубликатов в истории терминала',
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
        echo "m180730_104407_add_option_dublicate_terminal_history cannot be reverted.\n";

        return false;
    }
    */
}
