<?php

use yii\db\Migration;

/**
 * Class m180525_095722_add_message_templates
 */
class m180525_095722_add_message_templates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('chat_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '12',
                'synonym' => 'появление невыплаченных бонусов',
                'text' => 'Вам была начислена прибыль в размере {{sum}}$ от инвестирования бонусов. Общая сумма прибыли бонусов {{amount}}$. Для их получения необходимо {{need}}.',
                'comment' => 'Доступные переменные: {{sum}} - сумма начисленных бонусов. {{amount}} - сумма бонусов ожидающих перевода, {{need}} - требования',
            ],
            [
                'id' => '13',
                'synonym' => 'перевод невыплаченных бонусов на счет',
                'text' => 'Поздравляем! На Ваш счет было переведено {{amount}}$ от инвестирования бонусов.',
                'comment' => 'Доступные переменные: {{amount}} - сумма начисления (бонусов ожидавших перевода)',
            ],
        ]);
        $this->alterColumn('overdraft', 'summ', $this->double()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('chat_template', ['id' => [12,13]]);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_095722_add_message_templates cannot be reverted.\n";

        return false;
    }
    */
}
