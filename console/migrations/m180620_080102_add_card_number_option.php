<?php

use yii\db\Migration;

/**
 * Class m180620_080102_add_card_number_option
 */
class m180620_080102_add_card_number_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'deposit_card_number',
                'value' => '4274  2752  0164  3466',
                'description' => 'Номер карты для депозита переводом на карту',
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
        echo "m180620_080102_add_card_number_option cannot be reverted.\n";

        return false;
    }
    */
}
