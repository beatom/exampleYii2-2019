<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'pass_md5' => $this->string()->notNull(),
            'password_reset_token' => $this->string(),

            'username' => $this->string()->null()->unique(),
            'firstname' => $this->string()->null(),
            'lastname' => $this->string()->null(),
            'middlename' => $this->string()->null(),
            'date_bithday' => $this->date()->null()->defaultValue(NULL),
            'country_id' => $this->integer()->null()->defaultValue(NULL),
            'city_id' => $this->integer()->null()->defaultValue(NULL),
            'phone' => $this->string(50)->null()->defaultValue(NULL),
            'avatar' => $this->string()->null()->defaultValue(NULL)->comment('путь к аватару'),
            'sms_confirm' => $this->smallInteger(1)->null()->defaultValue(0)->comment('подтвержден'),
            'email_confirm' => $this->smallInteger(1)->null()->defaultValue(0)->comment('подтвержден'),
            'balance' => $this->integer()->null()->defaultValue(0)->comment('Не вложеные средства'),
            'balance_bonus' => $this->integer()->null()->defaultValue(0)->comment('Бонусные не вложеные средства'),
            'pamm_available' => $this->smallInteger()->Null()->defaultValue(1)->comment('разрешено создавать счета'),

            'partner_id' => $this->integer(11)->null()->defaultValue(NULL)->comment('id партера который пригласил, для партнерской программы'),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'date_reg' => $this->dateTime()->notNull(),
        ], $tableOptions);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }
}
