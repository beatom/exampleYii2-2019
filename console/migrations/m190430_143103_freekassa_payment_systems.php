<?php

use yii\db\Migration;

/**
 * Class m190430_143103_freekassa_payment_systems
 */
class m190430_143103_freekassa_payment_systems extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('payment_systems', ['title','system','via','sum_min','sum_max','image','currency_id', 'position'], [
            [
                'title' => 'Яндекс.Деньги',
                'system' => 'Freekassa',
                'via' => '45',
                'sum_min' => '10',
                'sum_max' => '10000',
                'image' => '/img/payment-3.png',
                'currency_id' => '4',
                'position' => '3',
            ],
            [
                'title' => 'Qiwi',
                'system' => 'Freekassa',
                'via' => '63',
                'sum_min' => '10',
                'sum_max' => '20000',
                'image' => '/img/payment-2.png',
                'currency_id' => '1',
                'position' => '4',
            ],
            [
                'title' => 'CARD P2P',
                'system' => 'Freekassa',
                'via' => '159',
                'sum_min' => '10',
                'sum_max' => '20000',
                'image' => '/img/payment-1.png',
                'currency_id' => '4',
                'position' => '5',
            ],
            [
                'title' => 'VISA/MASTERCARD',
                'system' => 'Freekassa',
                'via' => '160',
                'sum_min' => '10',
                'sum_max' => '20000',
                'image' => '/img/payment-1.png',
                'currency_id' => '4',
                'position' => '6',
            ],
            [
                'title' => 'Qiwi USD',
                'system' => 'Freekassa',
                'via' => '161',
                'sum_min' => '10',
                'sum_max' => '20000',
                'image' => '/img/payment-2.png',
                'currency_id' => '1',
                'position' => '7',
            ],
            [
                'title' => 'Qiwi EURO',
                'system' => 'Freekassa',
                'via' => '123',
                'sum_min' => '10',
                'sum_max' => '20000',
                'image' => '/img/payment-2.png',
                'currency_id' => '2',
                'position' => '8',
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

}
