<?php

use yii\db\Migration;

/**
 * Class m180523_071307_user_document_date_add
 */
class m180523_071307_user_document_date_add extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_document', 'date_add', $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_document', 'date_add');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180523_071307_user_document_date_add cannot be reverted.\n";

        return false;
    }
    */
}
