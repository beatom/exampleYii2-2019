<?php

use yii\db\Migration;

/**
 * Class m181107_145346_issue_90_updates
 */
class m181107_145346_issue_90_updates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('news', 'input_class', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('news', 'input_button_name', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('shares', 'input_class', $this->string(255)->null()->defaultValue(null));
        $this->addColumn('shares', 'input_button_name', $this->string(255)->null()->defaultValue(null));

        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('log_demo_bonus',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull(),
            'trading_account_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(2)->notNull()->defaultValue(1),
            'date_add' =>  $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions . ' COMMENT "Заявки со счетами по акции <Трейдинг за счёт компании!>"');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        $this->dropColumn('news', 'input_class');
        $this->dropColumn('news', 'input_button_name');
        $this->dropColumn('shares', 'input_class');
        $this->dropColumn('shares', 'input_button_name');
        $this->dropTable('log_demo_bonus');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181107_145346_issue_90_updates cannot be reverted.\n";

        return false;
    }
    */
}
