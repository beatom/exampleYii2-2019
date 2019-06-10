<?php

use yii\db\Migration;

/**
 * Class m190222_121759_unpartner_investments
 */
class m190222_121759_unpartner_investments extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->addColumn('investments', 'no_partner_benefit', $this->boolean()->notNull()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('investments', 'no_partner_benefit');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190222_121759_unpartner_investments cannot be reverted.\n";

        return false;
    }
    */
}
