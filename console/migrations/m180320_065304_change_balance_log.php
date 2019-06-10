<?php

use yii\db\Migration;

/**
 * Class m180320_065304_change_balance_log
 */
class m180320_065304_change_balance_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('balance_log', 'recipient_user_id', $this->integer(11)->null()->defaultValue(null)->comment('id пользователя получателя'));
        $this->insert('email_tempalte', [
            'id' => 7,
            'synonym' => 'Заявка на перевод средств',
            'title' => 'Новая заявка на перевод личних средств на сайте invest24.com',
            'text' => '<p>Пользователь: {{username}}</p><p>переводит {{summ}} $ пользователю {{resipientusername}}</p>',
            'comment' => 'Доступные переменные: {{username}} - имя отправителя, {{resipientusername}} - имя получателя, {{summ}} - сумма'
        ]);

        $this->insert('email_tempalte', [
            'id' => 8,
            'synonym' => 'Получение перевода средств',
            'title' => 'Получение перевода средств на сайте invest24.com',
            'text' => '<p>Пользователь: {{username}}</p><p>перевел {{summ}} $ пользователю {{resipientusername}}</p>',
            'comment' => 'Доступные переменные: {{username}} - имя отправителя, {{resipientusername}} - имя получателя, {{summ}} - сумма'
        ]);
        $this->insert('email_tempalte', [
            'id' => 9,
            'synonym' => 'Одобрение заявки',
            'title' => 'Ваша заявка на перевода средств выполнена',
            'text' => '<p>Заявка №{{id_log}} на сумму {{summ}} $ одобрена.</p><p>пользователю {{resipientusername}} средства уже поступили</p>',
            'comment' => 'Доступные переменные: {{username}} - имя отправителя, {{resipientusername}} - имя получателя, {{summ}} - сумма, {{id_log}}'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('balance_log', 'recipient_user_id');
        $this->execute('DELETE FROM `email_tempalte` WHERE `id` = 7 AND `id` = 8 AND `id` = 9');
        return true;
    }


}
