<?php

use yii\db\Migration;

/**
 * Class m180307_143852_update_user_doc_table
 */
class m180307_143852_update_user_doc_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_document', 'need_verification', $this->boolean()->notNull()->defaultValue(false));
        $this->addColumn('user', 'verified', $this->boolean()->notNull()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user_document', 'need_verification');
        $this->dropColumn('user', 'verified');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180307_143852_update_user_doc_table cannot be reverted.\n";

        return false;
    }
    */
}
