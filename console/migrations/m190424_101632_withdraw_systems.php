<?php

use yii\db\Migration;

/**
 * Class m190424_101632_withdraw_systems
 */
class m190424_101632_withdraw_systems extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('payment_systems_withdraw', [
            'id' => $this->primaryKey(),
            'title' => $this->string(255)->null()->defaultValue(null),
            'image' => $this->string(255)->null()->defaultValue(null),
            'currency_id' => $this->integer(11)->notNull(),
            'fee' => $this->double()->notNull()->defaultValue(0),
            'show' => $this->boolean()->notNull()->defaultValue(1),
            'position' => $this->integer(3)->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Платежные системы для вывода"');

        $this->batchInsert('payment_systems_withdraw', ['id', 'title', 'image', 'currency_id', 'position', 'fee'], [
            [
                'id' => '8',
                'title' => 'Банковская карта',
                'image' => '/img/payment-1.png',
                'currency_id' => '1',
                'position' => '1',
                'fee' => '3'
            ],
            [
                'id' => '1',
                'title' => 'Payeer',
                'image' => '/img/payment-4.png',
                'currency_id' => '0',
                'position' => '2',
                'fee' => '3'
            ],
            [
                'id' => '10',
                'title' => 'AdvCash',
                'image' => '/img/payment-advcash.png',
                'currency_id' => '0',
                'position' => '3',
                'fee' => '3'
            ],
            [
                'id' => '6',
                'title' => 'Qiwi',
                'image' => '/img/payment-2.png',
                'currency_id' => '1',
                'position' => '4',
                'fee' => '3'
            ],
            [
                'id' => '2',
                'title' => 'PerfectMoney',
                'image' => '/img/payment-5.png',
                'currency_id' => '0',
                'position' => '5',
                'fee' => '3'
            ],
            [
                'id' => '7',
                'title' => 'Яндекс.Деньги',
                'image' => '/img/payment-3.png',
                'currency_id' => '0',
                'position' => '6',
                'fee' => '3'
            ]
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('payment_systems_withdraw');
        return true;
    }
    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190424_101632_withdraw_systems cannot be reverted.\n";

        return false;
    }
    */
}
