<?php

use yii\db\Migration;

/**
 * Class m190410_085421_payment_systems
 */
class m190410_085421_payment_systems extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('payment_systems',[
            'id' => $this->primaryKey(),
            'title' =>  $this->string(255)->null()->defaultValue(null),
            'system' =>  $this->string(255)->null()->defaultValue(null),
            'via' =>  $this->string(255)->null()->defaultValue(null),
            'sum_min' => $this->integer(11)->notNull()->defaultValue(0),
            'sum_max' => $this->integer(11)->notNull()->defaultValue(0),
            'image' => $this->string(255)->null()->defaultValue(null),
            'currency_id' => $this->integer(11)->notNull(),
            'fee' => $this->double()->notNull()->defaultValue(0),
            'fee_add' => $this->double()->notNull()->defaultValue(0),
            'show' => $this->boolean()->notNull()->defaultValue(1),
            'comment' =>  $this->text()->null()->defaultValue(null),
            'position' => $this->integer(3)->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Платежные системы для пополнения"');

        $this->batchInsert('payment_systems', ['title','system','via','sum_min','sum_max','image','currency_id', 'position'], [
            [
                'title' => 'Payeer',
                //'synonym' => 'payeer',
                'system' => 'Payeer',
                'via' => '',
                'sum_min' => '10',
                'sum_max' => '10000',
                'image' => '/img/payment-4.png',
                'currency_id' => '1',
                'position' => '1',
            ],
            [
                'title' => 'PerfectMoney',
                //'synonym' => 'perfect',
                'system' => 'Perfectmoney',
                'via' => '',
                'sum_min' => '10',
                'sum_max' => '20000',
                'image' => '/img/payment-5.png',
                'currency_id' => '1',
                'position' => '2',
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('payment_systems');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190410_085421_payment_systems cannot be reverted.\n";

        return false;
    }
    */
}
