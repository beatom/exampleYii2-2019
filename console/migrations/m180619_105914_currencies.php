<?php

use yii\db\Migration;

/**
 * Class m180619_105914_currencies
 */
class m180619_105914_currencies extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('currencies', [
            'id' => $this->primaryKey(),
            'synonym' => $this->string(50)->notNull(),
            'value' => $this->double()->notNull()->defaultValue(0),
            'name' => $this->string(50)->null()->defaultValue(null),
            'updated_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => $this->boolean()->notNull()->defaultValue(1),
        ], $tableOptions . ' COMMENT "Курсы валют"');


        $this->batchInsert('currencies', ['synonym', 'value', 'name'], [
            [
                'synonym' => 'USD',
                'value' => '63.4838',
                'name' => 'Доллар США',
            ],
            [
                'synonym' => 'EUR',
                'value' => '73.4825',
                'name' => 'Евро',
            ],
        ]);


        $this->createTable('payment_card_requests', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'balance_log_id' => $this->integer(11)->notNull(),
            'card_number' => $this->string(255)->notNull(),
            'summ_rub' => $this->double()->notNull()->defaultValue(0),
            'summ_usd' => $this->double()->notNull()->defaultValue(0),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => $this->integer()->notNull()->defaultValue(1)->comment('1-новая заявка, 2-выполнено, 3-отменена'),
        ], $tableOptions . ' COMMENT "Заявки пополнения баланса вручную"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('currencies');
        $this->dropTable('payment_card_requests');
        return true;
    }
}
