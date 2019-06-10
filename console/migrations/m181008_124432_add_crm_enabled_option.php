<?php

use yii\db\Migration;

/**
 * Class m181008_124432_add_crm_enabled_option
 */
class m181008_124432_add_crm_enabled_option extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'amo_crm_enabled',
                'value' => '1',
                'description' => 'Связь с amoCrm',
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
        echo "m181008_124432_add_crm_enabled_option cannot be reverted.\n";

        return false;
    }
    */
}
