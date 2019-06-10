<?php

use yii\db\Migration;

/**
 * Class m181129_163820_update_webinar
 */
class m181129_163820_update_webinar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('webinar', 'show_as_active', $this->boolean()->notNull()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->dropColumn('webinar', 'show_as_active');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181129_163820_update_webinar cannot be reverted.\n";

        return false;
    }
    */
}
