<?php

use yii\db\Migration;

/**
 * Class m180313_114746_add_fb_id
 */
class m180313_114746_add_fb_id extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user', 'fb_id', $this->string(20)->null()->defaultValue(null)->unique()->after('email')->comment('id user in FB'));
        $this->addColumn('user', 'vk_id', $this->string(20)->null()->defaultValue(null)->unique()->after('email')->comment('id user in FB'));
        $this->alterColumn('user', 'email', $this->string()->null()->defaultValue(null));
        $this->addColumn('balance_log', 'comment', $this->string()->null()->defaultValue(null));
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'fb_id');
        $this->dropColumn('user', 'vk_id');
        $this->dropColumn('balance_log', 'comment');
        return true;
    }
}
