<?php

use yii\db\Migration;

/**
 * Class m190605_130112_freeobmen_payment_system
 */
class m190605_130112_freeobmen_payment_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('payment_systems', ['title', 'system', 'via', 'sum_min', 'sum_max', 'image', 'currency_id', 'position', 'fee'], [
                [
                    'title' => 'VISA/MASTERCARD',
                    'system' => 'FreeObmen',
                    'via' => 'Card',
                    'sum_min' => '10',
                    'sum_max' => '220',
                    'image' => '/img/payment-1.png',
                    'currency_id' => '1',
                    'position' => '14',
                    'fee' => 4
                ],
                [
                    'title' => 'Qiwi',
                    'system' => 'FreeObmen',
                    'via' => 'Qiwi',
                    'sum_min' => '80',
                    'sum_max' => '750',
                    'image' => '/img/payment-2.png',
                    'currency_id' => '1',
                    'position' => '15',
                    'fee' => 6
                ],
                [
                    'title' => 'Yandex деньги',
                    'system' => 'FreeObmen',
                    'via' => 'Yandex',
                    'sum_min' => '10',
                    'sum_max' => '220',
                    'image' => '/img/payment-3.png',
                    'currency_id' => '1',
                    'position' => '16',
                    'fee' => 5
                ]
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
        echo "m190605_130112_freeobmen_payment_system cannot be reverted.\n";

        return false;
    }
    */
}
