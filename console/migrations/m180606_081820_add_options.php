<?php

use yii\db\Migration;

/**
 * Class m180606_081820_add_options
 */
class m180606_081820_add_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'yandex_metrica',
                'value' => '',
                'description' => 'Код Яндекс метрики',
            ],
            [
                'key' => 'google_metrica',
                'value' => '',
                'description' => 'Код счетчика Google',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        
        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180606_081820_add_options cannot be reverted.\n";

        return false;
    }
    */
}
