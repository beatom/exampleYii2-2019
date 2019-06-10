<?php

use yii\db\Migration;

/**
 * Class m180612_084030_sms_template
 */
class m180612_084030_sms_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('sms_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '7',
                'synonym' => 'Уведомление после окончания бонусов',
                'text' => 'На сайте {{site_name}} у вас закончился срок действия бонусного счета. Общая сумма прибыли от бонусов составляет {{amount}}$. Для их получения необходимо {{need}}.',
                'comment' => 'Доступные переменные: {{site_name}} - название сайта,  {{amount}} - сумма бонусов ожидающих перевода, {{need}} - требования',
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
    
}
