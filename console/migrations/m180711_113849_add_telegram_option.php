<?php

use yii\db\Migration;

/**
 * Class m180711_113849_add_telegram_option
 */
class m180711_113849_add_telegram_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'telegram',
                'value' => 'https://t.me/invest24',
                'description' => 'Ссылка на телеграм канал',
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
        echo "m180711_113849_add_telegram_option cannot be reverted.\n";

        return false;
    }
    */
}
