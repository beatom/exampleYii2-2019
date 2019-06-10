<?php

use yii\db\Migration;

/**
 * Class m180525_131606_all_user_doc
 */
class m180525_131606_all_user_doc extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
	    $this->createTable('users_documents_uploaded',[
		    'id' => $this->primaryKey(),
		    'user_id' => $this->integer(11)->notNull(),
		    'file' => $this->string(255)->null()->defaultValue(NULL)->comment('путь к фото'),
		    'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
	    ], $tableOptions . ' COMMENT "Все документы пользователей"');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users_documents_uploaded');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_131606_all_user_doc cannot be reverted.\n";

        return false;
    }
    */
}
