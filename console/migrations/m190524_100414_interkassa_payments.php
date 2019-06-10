<?php

use yii\db\Migration;

/**
 * Class m190524_100414_interkassa_payments
 */
class m190524_100414_interkassa_payments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('payment_systems', ['title', 'system', 'via', 'sum_min', 'sum_max', 'image', 'currency_id', 'position', 'fee'], [
                [
                    'title' => 'Advcash',
                    'system' => 'Interkassa',
                    'via' => 'advcash',
                    'sum_min' => '10',
                    'sum_max' => '10000',
                    'image' => '/img/payment-advcash.png',
                    'currency_id' => '1',
                    'position' => '11',
                    'fee' => 0
                ],
                [
                    'title' => 'Qiwi Кошелек',
                    'system' => 'Interkassa',
                    'via' => 'qiwi',
                    'sum_min' => '10',
                    'sum_max' => '1000',
                    'image' => '/img/payment-2.png',
                    'currency_id' => '1',
                    'position' => '12',
                    'fee' => 0
                ],
                [
                    'title' => 'Yandex.Money',
                    'system' => 'Interkassa',
                    'via' => 'yandex',
                    'sum_min' => '10',
                    'sum_max' => '1000',
                    'image' => '/img/payment-3.png',
                    'currency_id' => '1',
                    'position' => '13',
                    'fee' => 0
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

}
