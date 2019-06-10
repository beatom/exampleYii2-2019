<?php

use yii\db\Migration;

/**
 * Class m180330_060012_partner_piramida
 */
class m180330_060012_partner_piramida extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_partner_info', 'ids_partner', $this->text()->null()->defaultValue(null)->comment('ids партнеров'));
        $this->addColumn('user', 'balance_partner', $this->integer(11)->defaultValue(0)->after('balance_bonus')->comment('партнерский счет'));
        $this->alterColumn('user', 'status_in_partner', $this->integer(11)->null()->defaultValue(0)->comment('статус в партнерке'));
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('balance_partner_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->unsigned(),
            'summ' => $this->integer(11)->null()->defaultValue(0),
            'status' => $this->integer(1)->defaultValue(0),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'description' => $this->string()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Партнерский счет"');

        $this->createTable('partner_basic_income',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->unsigned(),
            'summ' => $this->integer(11)->null()->defaultValue(0),
            'status' => $this->integer(1)->defaultValue(0),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'description' => $this->string()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Партнерский счет"');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo 'not revers';

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180330_060012_partner_piramida cannot be reverted.\n";

        return false;
    }
    */
}
