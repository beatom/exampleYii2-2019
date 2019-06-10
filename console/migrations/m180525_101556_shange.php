<?php

use yii\db\Migration;

/**
 * Class m180525_101556_shange
 */
class m180525_101556_shange extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->alterColumn('balance_partner_log', 'summ', $this->double()->notNull()->defaultValue(0));
	    $this->alterColumn('user', 'balance', $this->double()->notNull()->defaultValue(0));
	    $this->alterColumn('user', 'balance_partner', $this->double()->notNull()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180525_101556_shange cannot be reverted.\n";

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180525_101556_shange cannot be reverted.\n";

        return false;
    }
    */
}
