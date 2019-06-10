<?php

use yii\db\Migration;

/**
 * Class m180423_100832_edit_user
 */
class m180423_100832_edit_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'edit_username', $this->smallInteger(1)->defaultValue(0)->comment('разрешить редактировать'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180423_100832_edit_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180423_100832_edit_user cannot be reverted.\n";

        return false;
    }
    */
}
