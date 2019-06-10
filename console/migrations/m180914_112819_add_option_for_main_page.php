<?php

use yii\db\Migration;

/**
 * Class m180914_112819_add_option_for_main_page
 */
class m180914_112819_add_option_for_main_page extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'profits_brought',
                'value' => '501060',
                'description' => 'сумма выплат для калькулятора на главной',
            ]
        ]);
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
        echo "m180914_112819_add_option_for_main_page cannot be reverted.\n";

        return false;
    }
    */
}
