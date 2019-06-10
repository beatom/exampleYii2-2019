<?php

use yii\db\Migration;

/**
 * Handles the creation of table `chat_template`.
 */
class m180227_152136_create_chat_template_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('chat_template',[
            'id' => $this->primaryKey(),
            'synonym' => $this->string(255)->null(),
            'text' => $this->text()->notNull()->comment('Текст c переменными'),
            'comment' => $this->string(255)->notNull(),
            'sender_id' => $this->integer()->null()
        ], $tableOptions . ' COMMENT "Шаблоны автоматических сообщений чата"');

        $this->batchInsert('chat_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '1',
                'synonym' => 'Регистрация',
                'text' => 'Поздравляем с регистрацией на сайте {{site_name}}.',
                'comment' => 'Доступные переменные: {{site_name}} - название сайта.',
            ],
            [
                'id' => '2',
                'synonym' => 'Одобрение заявки вывода средств',
                'text' => 'Ваша заявка на вывод {{amount}} {{currency}} через сервис {{service}} на счет {{invoice}} была успешно выполнена.',
                'comment' => 'Доступные переменные: {{amount}} - сумма вывода, {{currency}} - валюта, {{service}} - сервис, {{invoice}} номер счета/кошелька',
            ],
            [
                'id' => '3',
                'synonym' => 'Отклонение заявки вывода средств',
                'text' => 'Ваша заявка на вывод {{amount}} {{currency}} через сервис {{service}} на счет {{invoice}} была отклонена.',
                'comment' => 'Доступные переменные: {{amount}} - сумма вывода, {{currency}} - валюта, {{service}} - сервис, {{invoice}} номер счета/кошелька',
            ],
            [
                'id' => '4',
                'synonym' => 'Одобрение заявки перевода средств',
                'text' => 'Ваша заявка на перевод {{amount}} {{currency}} пользователю {{username}} была успешно выполнена.',
                'comment' => 'Доступные переменные: {{amount}} - сумма вывода, {{currency}} - валюта, {{username}} - логин пользователю которому переводят средства',
            ],
            [
                'id' => '5',
                'synonym' => 'Отклонение заявки перевода средств',
                'text' => 'Ваша заявка на перевод {{amount}} {{currency}} пользователю {{username}} была отклонена.',
                'comment' => 'Доступные переменные: {{amount}} - сумма вывода, {{currency}} - валюта, {{username}} - логин пользователю которому переводят средства',
            ],
            [
                'id' => '6',
                'synonym' => 'Достижение партнерского статуса',
                'text' => 'Поздравляем, Вы достигли нового партнерского статуса {{statusname}}.',
                'comment' => 'Доступные переменные: {{statusname}} - название партнерского статуса.',
            ],
            [
                'id' => '7',
                'synonym' => 'Cрабатывание ф-ции защиты капитала',
                'text' => 'Сегодня в {{time}} сработала функция защиты капитала по вашему счету {{invoice}} в связи достижением критического лимита по счету.',
                'comment' => 'Доступные переменные: {{time}} - время, {{invoice}} - номер счета/инвестиции.',
            ],
            [
                'id' => '8',
                'synonym' => 'Начисление бонусов',
                'text' => 'На Ваш счет были начислены бонусы в размере {{amount}} {{currency}}.',
                'comment' => 'Доступные переменные: {{amount}} - сумма вывода, {{currency}} - валюта',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('chat_template');
    }
}
