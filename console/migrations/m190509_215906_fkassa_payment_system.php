<?php

use yii\db\Migration;

/**
 * Class m190509_215906_fkassa_payment_system
 */
class m190509_215906_fkassa_payment_system extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('payment_systems', ['title', 'system', 'via', 'sum_min', 'sum_max', 'image', 'currency_id', 'position', 'fee'], [
                [
                    'title' => 'Qiwi',
                    'system' => 'Fkassa',
                    'via' => '2',
                    'sum_min' => '10',
                    'sum_max' => '1000',
                    'image' => '/img/payment-2.png',
                    'currency_id' => '1',
                    'position' => '9',
                    'fee' => 2
                ],
                [
                    'title' => 'Банковская карта',
                    'system' => 'Fkassa',
                    'via' => '1',
                    'sum_min' => '10',
                    'sum_max' => '1000',
                    'image' => '/img/payment-1.png',
                    'currency_id' => '1',
                    'position' => '10',
                    'fee' => 2
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
        echo "m190509_215906_fkassa_payment_system cannot be reverted.\n";

        return false;
    }
    */
}
