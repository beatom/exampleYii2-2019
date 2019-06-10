<?php

use yii\db\Migration;

/**
 * Class m190607_133056_payin_payment_system
 */
class m190607_133056_payin_payment_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('payment_systems', ['title', 'system', 'via', 'sum_min', 'sum_max', 'image', 'currency_id', 'position', 'fee'], [
                [
                    'title' => 'VISA/MASTERCARD/МИР',
                    'system' => 'PayinPayout',
                    'via' => null,
                    'sum_min' => '10',
                    'sum_max' => '2000',
                    'image' => '/img/payment-1.png',
                    'currency_id' => '1',
                    'position' => '17',
                    'fee' => 10
                ],
             
            ]
        );
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
        echo "m190607_133056_payin_payment_system cannot be reverted.\n";

        return false;
    }
    */
}
