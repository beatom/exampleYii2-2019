<?php

use yii\db\Migration;

/**
 * Class m180606_095125_investment_deleted
 */
class m180606_095125_investment_deleted extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('investments', 'deleted', $this->boolean()->notNull()->defaultValue(false));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('investment_delete_log', [
            'id' => $this->primaryKey(),
            'investment_id' => $this->integer(11)->notNull(),
            'date_add' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
            'status' => $this->boolean()->notNull()->defaultValue(0),
        ], $tableOptions . ' COMMENT "Лог закрытия инвестиций"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('investments', 'deleted');
        $this->dropTable('investment_delete_log');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180606_095125_investment_deleted cannot be reverted.\n";

        return false;
    }
    */
}
