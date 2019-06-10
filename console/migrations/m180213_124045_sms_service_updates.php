<?php

use yii\db\Migration;

/**
 * Class m180213_124045_sms_service_updates
 */
class m180213_124045_sms_service_updates extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        //соц сети
        $this->createTable('sms_managers',[
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'api_login' => $this->string(255)->notNull(),
            'api_password' => $this->string(255)->notNull(),
            'is_active' => $this->boolean()->notNull()->defaultValue(0),
            'class_name' => $this->string(255)->notNull(),
        ], $tableOptions . ' COMMENT "сервисы отправки СМС"');


        $this->batchInsert('sms_managers', [ 'name', 'api_login', 'api_password', 'is_active', 'class_name'], [
            [
                'name' => 'Смс Дисконт',
                'api_login' => 'z1518522256913',
                'api_password' => '993353',
                'is_active' => 1,
                'class_name' => 'iqsms',
            ],
            [
                'name' => 'Простор Смс',
                'api_login' => 'login',
                'api_password' => 'password',
                'is_active' => 0,
                'class_name' => 'prostor',
            ],
        ]);

        $this->addColumn('sms_template', 'comment', $this->string(255)->notNull());
        $this->addColumn('sms_log', 'sms_manager_id', $this->string(255)->notNull());
        $this->addColumn('sms_log', 'smscId', $this->string(80)->notNull()->comment('id для проверки статуса смс'));

        $this->batchInsert('sms_template', [ 'id', 'synonym', 'text', 'comment'], [
            [
                'id' => '1',
                'synonym' => 'Подтверждение регистрации',
                'text' => 'Ваш код для подтверждения регистрации на сайте {{site_name}}: {{code}}.',
                'comment' => 'Доступные переменные: {{site_name}} - название сайта, {{code}} - код подтвреждения.',
            ],
            [
                'id' => '2',
                'synonym' => 'Потверждение вывода средств',
                'text' => 'Ваш код для подтверждения вывода средств : {{code}}.',
                'comment' => 'Доступные переменные: {{code}} - код подтвреждения.',
            ],
            [
                'id' => '3',
                'synonym' => 'Потверждение перевода средств другому пользователю.',
                'text' => 'Ваш код для подтверждения вывода средств : {{code}}.',
                'comment' => 'Доступные переменные: {{code}} - код подтвреждения.',
            ],
            [
                'id' => '4',
                'synonym' => 'Подтверждение телефона в личном кабинете',
                'text' => 'Ваш код для подтверждения вывода средств : {{code}}.',
                'comment' => 'Доступные переменные: {{code}} - код подтвреждения.',
            ],
            [
                'id' => '5',
                'synonym' => 'Подтверждение смены пароля',
                'text' => 'Ваш код для подтверждения смены пароля личного кабинета на сайте {{site_name}}: {{code}}.',
                'comment' => 'Доступные переменные: {{site_name}} - название сайта, {{code}} - код подтвреждения.',
            ],
            [
                'id' => '6',
                'synonym' => 'Отключение смс уведомления при выводе стредств',
                'text' => 'Ваш код для подтверждения отключения услуги смс защиты при выводе свредств личного кабинета: {{code}}.',
                'comment' => 'Доступные переменные: {{code}} - код подтвреждения.',
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('sms_managers');
        $this->dropColumn('sms_template', 'comment');
        $this->dropColumn('sms_log', 'sms_manager_id');
        $this->dropColumn('sms_log', 'smscId');

        return true;
    }
}
