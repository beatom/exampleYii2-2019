<?php

use yii\db\Migration;

/**
 * Class m181216_130044_issue_106_additional_sms_template
 */
class m181216_130044_issue_106_additional_sms_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('sms_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '9',
                'synonym' => 'Подтверждение Записи на посещение',
                'text' => 'Ваш код для подтверждения заявки на встречу: {{code}}.',
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
        echo "m181216_130044_issue_106_additional_sms_template cannot be reverted.\n";

        return false;
    }
    */
}
