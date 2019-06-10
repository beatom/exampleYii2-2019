<?php

use yii\db\Migration;

/**
 * Class m190411_130044_add_sms_template
 */
class m190411_130044_add_sms_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('sms_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '10',
                'synonym' => 'Смена платежной системы',
                'text' => 'Ваш код для подтверждения измения реквезитов для вывода средств: {{code}}.',
                'comment' => 'Доступные переменные: {{code}} - код подтвреждения.',
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
        echo "m190411_130044_add_sms_template cannot be reverted.\n";

        return false;
    }
    */
}
