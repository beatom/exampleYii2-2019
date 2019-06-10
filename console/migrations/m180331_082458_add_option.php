<?php

use yii\db\Migration;

/**
 * Class m180331_082458_add_option
 */
class m180331_082458_add_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'exchange_rate',
                'value' => '1|1',
                'description' => 'курс обмена $=C',
            ],
        ]);

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('notification',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11),
            'notification' => $this->text()->null()->defaultValue(null)->comment('оповещение'),
            'type' => $this->smallInteger(1)->defaultValue(0)->comment('признак оповещения'),
            'status' => $this->smallInteger(1)->defaultValue(0)->comment('просмотрен'),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . ' COMMENT "оповещения пользоватедя"');
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
        echo "m180331_082458_add_option cannot be reverted.\n";

        return false;
    }
    */
}
