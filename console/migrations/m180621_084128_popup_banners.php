<?php

use yii\db\Migration;

/**
 * Class m180621_084128_popup_banners
 */
class m180621_084128_popup_banners extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'popup_banner_shown', $this->boolean()->notNull()->defaultValue(0));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
       $this->dropColumn('user', 'popup_banner_shown');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180621_084128_popup_banners cannot be reverted.\n";

        return false;
    }
    */
}
