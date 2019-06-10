<?php

use yii\db\Migration;

/**
 * Class m180607_133309_jivosite_code
 */
class m180607_133309_jivosite_code extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'jivosite_code',
                'value' => '',
                'description' => 'Код Jivosite',
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

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180607_133309_jivosite_code cannot be reverted.\n";

        return false;
    }
    */
}
