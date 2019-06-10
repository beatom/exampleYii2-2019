<?php

use yii\db\Migration;

/**
 * Class m180328_125215_partnerka
 */
class m180328_125215_partnerka extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'ball_invest', $this->integer(11)->defaultValue(0)->after('balance_bonus')->comment('баллы в invest'));
        $this->addColumn('balance_bonus_log', 'work_days', $this->integer(11)->defaultValue(0)->after('date_end')->comment('срок работы бонусов'));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('partner_balu_log',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->unsigned(),
            'ball' => $this->integer(11)->null()->defaultValue(0)->comment('балы от партнерки'),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'description' => $this->string()->null()->defaultValue(null),
        ], $tableOptions . ' COMMENT "Балы от партнерки"');

        $this->addColumn('user_partner_info', 'attraction_investors_item', $this->integer(2)->defaultValue(0)->comment('ид в масиве вознаграждений, уже начисленый'));
        $this->addColumn('user_partner_info', 'attraction_investors_count', $this->integer(11)->defaultValue(0)->comment('количество партнеров, которые инвестировали'));

        $this->addColumn('user_partner_info', 'attraction_partner_item', $this->integer(2)->defaultValue(0)->comment('ид в масиве вознаграждений, уже начисленый'));
        $this->addColumn('user_partner_info', 'attraction_partner_count', $this->integer(11)->defaultValue(0)->comment('количество партнеров'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        echo "m180328_125215_partnerka cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180328_125215_partnerka cannot be reverted.\n";

        return false;
    }
    */
}
