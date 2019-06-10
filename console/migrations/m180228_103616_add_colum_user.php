<?php

use yii\db\Migration;

/**
 * Class m180228_103616_add_colum_user
 */
class m180228_103616_add_colum_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'sms_code_money', $this->smallInteger(1)->null()->defaultValue(0)->after('sms_confirm')->comment('при выводе средств получать смс код'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user', 'sms_code_money');

        return true;
    }
}
