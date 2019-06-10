<?php

use yii\db\Migration;

/**
 * Class m181009_101448_add_admin_login_sms_template
 */
class m181009_101448_add_admin_login_sms_template extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'logout_admin',
                'value' => '0',
                'description' => 'Закрытие сессий в админке',
            ],
        ]);

        $this->batchInsert('sms_template', [ 'synonym', 'text', 'comment'], [
            [
                'synonym' => 'Код при входе в админку',
                'text' => 'Ваш пароль для входа в панель администратора: {{code}}',
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
        echo "m181009_101448_add_admin_login_sms_template cannot be reverted.\n";

        return false;
    }
    */
}
