<?php

use yii\db\Migration;

/**
 * Class m181010_082019_add_admin_login_options
 */
class m181010_082019_add_admin_login_options extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->batchInsert('options', [ 'key', 'value', 'description'], [
            [
                'key' => 'login_admin_white_ip',
                'value' => '127.0.0.1,46.149.86.15',
                'description' => 'Ip для входа в админку без запроса пароля',
            ],
            [
                'key' => 'login_admin_universal_key',
                'value' => '23452345234',
                'description' => 'Универсальный ключи для входа в админку без запроса пароля',
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
        echo "m181010_082019_add_admin_login_options cannot be reverted.\n";

        return false;
    }
    */
}
